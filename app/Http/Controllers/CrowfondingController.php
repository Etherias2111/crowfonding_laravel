<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ComponentCategory;
use App\Models\Ticket;
use App\Models\User;
use App\Actions\Fortify\CreateNewUser;
use App\Concerns\PasswordValidationRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrowfondingController extends Controller
{
    use PasswordValidationRules;
    public function home(): View
    {
        return view('crow.home', [
            'products' => Product::where('category', 'equipment')->latest()->get(),
            'mugs' => Product::where('category', 'mug')->latest()->get(),
            'componentCategories' => ComponentCategory::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function components(): View
    {
        return view('crow.components', [
            'categories' => ComponentCategory::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function componentCategory(ComponentCategory $componentCategory): View
    {
        return view('crow.component-category', [
            'category' => $componentCategory,
            'products' => $componentCategory->products()->latest()->paginate(12),
        ]);
    }

    public function product(Product $product): View
    {
        return view('crow.product', compact('product'));
    }

    public function privateMedia(string $type, string $filename): StreamedResponse
    {
        abort_unless(in_array($type, ['avatars', 'products'], true), 404);
        abort_unless(preg_match('/\A[a-f0-9]{32}\.(?:jpg|jpeg|png|webp)\z/i', $filename), 404);

        $path = "crowfonding/{$type}/{$filename}";
        $disk = Storage::disk('local');

        abort_unless($disk->exists($path), 404);

        $contentType = $disk->mimeType($path);
        abort_unless(in_array($contentType, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

        return $disk->response($path, $filename, [
            'Content-Type' => $contentType,
            'X-Content-Type-Options' => 'nosniff',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Cache-Control' => 'public, max-age=86400, immutable',
        ], 'inline');
    }

    public function services(): View
    {
        $lastTicket = request()->user()?->tickets()->latest()->first();
        $nextTicketAt = $lastTicket?->created_at?->copy()->addMinutes(60);
        return view('crow.services', compact('nextTicketAt'));
    }

    public function storeTicket(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service' => ['required', Rule::in(['Formateo de Equipos', 'Diagnóstico Técnico', 'Limpieza Física y Preventiva', 'Instalación de Componentes (RAM/SSD)', 'Diseño Publicitario / Branding'])],
            'note' => ['required', 'string', 'max:5000'],
            'address' => ['required_if:pickup,1', 'nullable', 'string', 'max:500'],
            'pickup' => ['nullable', 'boolean'],
        ], [
            'service.required' => 'Selecciona el servicio que necesitas.',
            'note.required' => 'Describe tu solicitud.',
            'address.required_if' => 'Escribe la dirección de recogida o desmarca la opción de recogida a domicilio.',
        ]);

        $ticket = DB::transaction(function () use ($request, $data) {
            User::whereKey($request->user()->id)->lockForUpdate()->first();
            $recent = $request->user()->tickets()->where('created_at', '>', now()->subMinutes(60))->exists();
            if ($recent) return null;
            return $request->user()->tickets()->create([
                'service' => $data['service'],
                'note' => $data['note'],
                'address' => ! empty($data['pickup']) ? ($data['address'] ?? '') : 'Sin recogida a domicilio',
                'status' => 'Recibido en sistema',
            ]);
        });

        if (! $ticket) {
            return back()->withInput()->withErrors(['cooldown' => 'No puedes enviar otro ticket todavía. Podrás crear una nueva solicitud cuando termine el tiempo de espera de 60 minutos.']);
        }
        try {
            $admins = User::where('role', 'admin')->pluck('email')->all();
            if ($admins) Mail::raw("Se registró un nuevo ticket (#{$ticket->id}). Inicia sesión en CROWFONDING y abre el panel de administración para consultar sus detalles.", function ($message) use ($admins) {
                $message->to(array_shift($admins))->bcc($admins)->subject('Nuevo ticket CROWFONDING');
            });
        } catch (\Throwable $exception) {
            Log::warning('No se pudo enviar la notificación del nuevo ticket.', ['exception' => $exception::class]);
        }
        try {
            Mail::raw("Tu solicitud #{$ticket->id} fue recibida.\nServicio: {$ticket->service}\nEstado: {$ticket->status}\n\nPuedes consultar su avance iniciando sesión en CROWFONDING.", fn ($message) => $message->to($request->user()->email)->subject('Recibimos tu ticket CROWFONDING #'.$ticket->id));
        } catch (\Throwable $exception) {
            Log::warning('No se pudo enviar la confirmación del ticket al cliente.', ['exception' => $exception::class]);
        }
        return redirect()->route('dashboard')->with('status', 'Solicitud registrada. Tu número de ticket es #'.$ticket->id.'.');
    }

    public function dashboard(): View
    {
        return view('crow.dashboard', ['tickets' => request()->user()->tickets()->latest()->get()]);
    }

    public function profile(): View
    {
        return view('crow.profile');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($request->user()->id), function ($attribute, $value, $fail) use ($request) {
                if (User::whereRaw('LOWER(username) = ?', [Str::lower($value)])->where('id', '<>', $request->user()->id)->exists()) $fail('Ese nombre de usuario ya está registrado.');
            }],
            'phone' => ['required', 'regex:/^\+?[0-9]{7,15}$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = bin2hex(random_bytes(16)).'.'.$file->extension();
            Storage::disk('local')->putFileAs('crowfonding/avatars', $file, $filename);
            $data['avatar'] = 'private/avatars/'.$filename;
        }
        $request->user()->update($data);
        return back()->with('status', 'Perfil actualizado.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        Validator::make($request->all(), [
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.confirmed' => 'Las contraseñas nuevas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 12 caracteres.',
            'required' => 'Completa este campo.',
        ])->validate();
        $request->user()->forceFill(['password' => $request->input('password')])->save();
        return back()->with('status', 'Contraseña actualizada correctamente.');
    }

    public function admin(): View
    {
        return view('crow.admin', [
            'tickets' => Ticket::with('user')->latest()->get(),
            'products' => Product::with('componentCategory')->latest()->get(),
            'componentCategories' => ComponentCategory::withCount('products')->orderBy('name')->get(),
            'users' => User::latest()->get(),
            'statuses' => ['Recibido en sistema', 'En diagnóstico', 'En reparación', 'Listo para entrega'],
        ]);
    }

    public function updateTicket(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['Recibido en sistema', 'En diagnóstico', 'En reparación', 'Listo para entrega'])]]);
        $statusChanged = $ticket->status !== $data['status'];
        $ticket->update($data);
        if (! $statusChanged) {
            return back()->with('status', 'El ticket ya estaba en ese estado; no se envió un correo duplicado.');
        }

        if (! $ticket->user?->email) {
            return back()->with('mail_warning', 'El estado se actualizó, pero la cuenta del cliente no tiene un correo asociado.');
        }

        try {
            Mail::raw("El estado de tu ticket #{$ticket->id} cambió a: {$ticket->status}.\nServicio: {$ticket->service}\n\nInicia sesión en CROWFONDING para consultar los detalles.", fn ($message) => $message->to($ticket->user->email)->subject('Actualización del ticket CROWFONDING #'.$ticket->id));
        } catch (\Throwable $exception) {
            Log::warning('No se pudo enviar la actualización del ticket al cliente.', ['exception' => $exception::class]);
            return back()->with('mail_warning', 'El estado se actualizó, pero no se pudo enviar el correo. Revisa la conexión y las credenciales SMTP del .env.');
        }

        return back()->with('status', 'Estado actualizado y notificación aceptada por el servidor de correo.');
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'], 'price' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:10000'],
            'category' => ['required', Rule::in(['equipment', 'mug', 'component'])],
            'component_category_id' => ['exclude_unless:category,component', 'required', 'integer', Rule::exists('component_categories', 'id')],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        if ($request->hasFile('image')) {
            $file = $request->file('image'); $filename = 'prod_'.bin2hex(random_bytes(16)).'.'.$file->extension();
            $filename = substr($filename, 5);
            Storage::disk('local')->putFileAs('crowfonding/products', $file, $filename);
            $data['image'] = 'private/products/'.$filename;
        }
        Product::create($data);
        return back()->with('status', match ($data['category']) {
            'mug' => 'Mug publicado.',
            'component' => 'Componente publicado en su categoría.',
            default => 'Equipo publicado.',
        });
    }

    public function storeComponentCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('component_categories', 'name')],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug !== '' ? $baseSlug : 'categoria';
        $suffix = 2;
        while (ComponentCategory::where('slug', $slug)->exists()) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'categoria').'-'.$suffix++;
        }

        ComponentCategory::create($data + ['slug' => $slug]);

        return back()->with('status', 'Categoría de componentes creada.');
    }

    public function deleteComponentCategory(ComponentCategory $componentCategory): RedirectResponse
    {
        if ($componentCategory->products()->exists()) {
            return back()->withErrors(['component_category' => 'No puedes eliminar esta categoría mientras tenga productos. Mueve o elimina primero sus productos.']);
        }

        $componentCategory->delete();

        return back()->with('status', 'Categoría eliminada.');
    }

    public function deleteProduct(Product $product): RedirectResponse
    {
        $product->delete();
        return back()->with('status', 'Equipo eliminado.');
    }

    public function storeUser(Request $request, CreateNewUser $creator): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::in(['cliente', 'admin'])]]);
        $user = $creator->create($request->only([
            'person_type', 'first_name', 'middle_name', 'first_surname', 'second_surname', 'document_type', 'document_number',
            'tax_id', 'legal_name', 'representative_name', 'commercial_name',
            'phone', 'phone_secondary', 'address', 'department', 'city', 'email', 'password', 'password_confirmation',
        ]));
        $user->update(['role' => $data['role']]);
        return back()->with('status', 'Cuenta creada correctamente.');
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::in(['cliente', 'admin'])]]);
        if ($user->is(auth()->user()) && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'No puedes quitarte tu propio rol de administrador.']);
        }
        if ($user->role === 'admin' && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['role' => 'Debe quedar al menos una cuenta administradora.']);
        }
        $user->update($data);
        return back()->with('status', 'Rol de usuario actualizado.');
    }
}
