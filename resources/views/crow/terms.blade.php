@extends('layouts.crow', ['title' => 'Términos y condiciones | CROWFONDING'])

@section('content')
<section class="crow-section terms-page">
    <div class="section-heading">
        <p class="eyebrow">Información para clientes</p>
        <h1>Términos y condiciones</h1>
        <p>Versión publicada el 6 de octubre de 2026. Consulta esta página antes de registrarte o solicitar un servicio.</p>
    </div>

    <article class="terms-content">
        <h2>1. Uso de la plataforma</h2>
        <p>La cuenta permite gestionar solicitudes de servicio, consultar su estado y recibir comunicaciones relacionadas. El usuario debe entregar datos de contacto correctos y mantener sus credenciales protegidas.</p>

        <h2>2. Recepción y diagnóstico de equipos</h2>
        <p>Cuando CROWFONDING reciba un equipo para prestar un servicio, se dejará constancia de recepción con la información aplicable, como fecha, propietario o persona que entrega, contacto, identificación y estado del equipo, servicio solicitado, valores pagados y fechas estimadas cuando puedan determinarse. El usuario debe revisar la constancia y comunicar cualquier diferencia.</p>
        <p>CROWFONDING asume el deber de custodia y conservación del bien durante el servicio y documentará las novedades, reparaciones y entrega. Las condiciones concretas, alcance, costos, garantía y fecha prevista se informarán para cada solicitud.</p>

        <h2>3. Equipos pendientes de retiro</h2>
        <p>Si un equipo no se retira en la fecha prevista, el procedimiento de contacto y custodia se aplicará conforme al artículo 18 de la Ley 1480 de 2011 y al Decreto 1413 de 2018. En términos generales, cuando haya transcurrido un mes desde la fecha prevista sin retiro, el prestador debe requerir por escrito al consumidor para que retire el bien dentro de los dos meses siguientes a la remisión de la comunicación. El aviso debe enviarse por los medios y a las direcciones previstos en la reglamentación. El cómputo y las excepciones se determinan según las circunstancias y la norma vigente.
        </p>
        <p>Los recordatorios por correo electrónico de esta plataforma son avisos complementarios; no sustituyen el requerimiento formal exigido por la reglamentación. Si el usuario se comunica e informa que no puede retirar el bien y una fecha probable, se revisará el caso de acuerdo con el procedimiento legal y las condiciones de custodia aplicables. No se declarará automáticamente abandonado un equipo solo por el paso de tres meses desde su entrega.</p>

        <h2>4. Notificaciones</h2>
        <p>La plataforma puede mostrar y enviar mensajes sobre registro, solicitudes, cambios de estado, diagnóstico, entrega y plazos de retiro. El usuario debe mantener actualizado su correo y consultar también las notificaciones visibles en su cuenta.</p>

        <h2>5. Derechos del consumidor</h2>
        <p>Estos términos no limitan los derechos irrenunciables reconocidos por la ley. Para cada servicio prevalecen la información y constancias entregadas al consumidor y las normas colombianas aplicables.</p>

        <h2>6. Normativa de referencia</h2>
        <ul>
            <li><a href="https://www.funcionpublica.gov.co/eva/gestornormativo/norma.php?i=44306" target="_blank" rel="noopener noreferrer">Ley 1480 de 2011, artículo 18 (Estatuto del Consumidor)</a>.</li>
            <li><a href="https://www1.funcionpublica.gov.co/eva/gestornormativo/norma.php?i=87866" target="_blank" rel="noopener noreferrer">Decreto 1413 de 2018</a>.</li>
        </ul>
    </article>
</section>
@endsection
