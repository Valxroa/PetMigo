<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include './conexion.php';

$id_usuario = $_SESSION['id_usuario'];

$nombre = $_SESSION['nombre'];


// ========================================
// CONTAR MASCOTAS
// ========================================

$sql_mascotas = "SELECT COUNT(*) AS total
                 FROM mascota
                 WHERE id_usuario = ?";

$stmt_mascotas = $conexion->prepare($sql_mascotas);

$stmt_mascotas->bind_param(
    "i",
    $id_usuario
);

$stmt_mascotas->execute();

$resultado_mascotas = $stmt_mascotas->get_result();

$datos_mascotas = $resultado_mascotas->fetch_assoc();

$total_mascotas = $datos_mascotas['total'];

$stmt_mascotas->close();


// ========================================
// CONTAR VACUNAS
// SOLO DE LAS MASCOTAS DEL USUARIO
// ========================================

$sql_vacunas = "SELECT COUNT(*) AS total
                FROM vacuna v
                INNER JOIN mascota m
                    ON v.id_mascota = m.id_mascota
                WHERE m.id_usuario = ?";

$stmt_vacunas = $conexion->prepare($sql_vacunas);

$stmt_vacunas->bind_param(
    "i",
    $id_usuario
);

$stmt_vacunas->execute();

$resultado_vacunas = $stmt_vacunas->get_result();

$datos_vacunas = $resultado_vacunas->fetch_assoc();

$total_vacunas = $datos_vacunas['total'];

$stmt_vacunas->close();


// ========================================
// CONTAR RECORDATORIOS PENDIENTES
// SOLO DE LAS MASCOTAS DEL USUARIO
// ========================================

$sql_recordatorios = "SELECT COUNT(*) AS total
                       FROM recordatorio r
                       INNER JOIN mascota m
                           ON r.id_mascota = m.id_mascota
                       WHERE m.id_usuario = ?
                       AND r.estado = 'Pendiente'";

$stmt_recordatorios = $conexion->prepare($sql_recordatorios);

$stmt_recordatorios->bind_param(
    "i",
    $id_usuario
);

$stmt_recordatorios->execute();

$resultado_recordatorios = $stmt_recordatorios->get_result();

$datos_recordatorios = $resultado_recordatorios->fetch_assoc();

$total_recordatorios = $datos_recordatorios['total'];

$stmt_recordatorios->close();


// ========================================
// PRÓXIMAS CITAS
// SOLO DE LAS MASCOTAS DEL USUARIO
// ========================================

$sql_proximas_citas = "SELECT
                            c.fecha,
                            c.hora,
                            c.motivo,
                            c.lugar,
                            c.estado,
                            m.nombre AS mascota
                       FROM cita c
                       INNER JOIN mascota m
                           ON c.id_mascota = m.id_mascota
                       WHERE m.id_usuario = ?
                       AND c.fecha >= CURDATE()
                       ORDER BY c.fecha ASC,
                                c.hora ASC
                       LIMIT 5";

$stmt_proximas_citas = $conexion->prepare($sql_proximas_citas);

$stmt_proximas_citas->bind_param(
    "i",
    $id_usuario
);

$stmt_proximas_citas->execute();

$resultado_proximas_citas = $stmt_proximas_citas->get_result();


// ========================================
// PRÓXIMOS RECORDATORIOS
// SOLO DE LAS MASCOTAS DEL USUARIO
// ========================================

$sql_proximos_recordatorios = "SELECT
                                    r.fecha,
                                    r.hora,
                                    r.titulo,
                                    r.descripcion,
                                    r.estado,
                                    m.nombre AS mascota
                               FROM recordatorio r
                               INNER JOIN mascota m
                                   ON r.id_mascota = m.id_mascota
                               WHERE m.id_usuario = ?
                               AND r.estado = 'Pendiente'
                               AND r.fecha >= CURDATE()
                               ORDER BY r.fecha ASC,
                                        r.hora ASC
                               LIMIT 5";

$stmt_proximos_recordatorios = $conexion->prepare(
    $sql_proximos_recordatorios
);

$stmt_proximos_recordatorios->bind_param(
    "i",
    $id_usuario
);

$stmt_proximos_recordatorios->execute();

$resultado_proximos_recordatorios =
    $stmt_proximos_recordatorios->get_result();


// ========================================
// CONSEJOS Y DATOS CURIOSOS
// ========================================

$consejos_del_dia = [

    "Recuerda mantener agua fresca y limpia disponible para tu mascota durante todo el día.",
    "Dedica unos minutos al día para jugar con tu mascota y estimular su actividad física.",
    "Mantén actualizado el calendario de vacunas y controles veterinarios de tu mascota.",
    "Cepilla regularmente a tu mascota para ayudar a mantener su pelaje limpio y saludable.",
    "Observa diariamente el comportamiento de tu mascota y presta atención a cambios importantes.",
    "Mantén limpia la zona donde duerme tu mascota para favorecer un ambiente saludable.",
    "Establece horarios regulares para alimentar a tu mascota y evita cambios bruscos en su rutina.",
    "Dedica tiempo de calidad a tu mascota, ya que la interacción también forma parte de sus cuidados.",
    "Consulta con un veterinario antes de realizar cambios importantes en la alimentación de tu mascota.",
    "Mantén los recipientes de agua y comida limpios y libres de restos acumulados.",
    "Revisa periódicamente las uñas de tu mascota y consulta con un profesional si necesitas cortarlas.",
    "Evita dejar productos de limpieza al alcance de tus mascotas.",
    "Guarda los medicamentos en lugares donde tu mascota no pueda alcanzarlos.",
    "Asegúrate de que tu mascota tenga un lugar tranquilo donde pueda descansar sin interrupciones.",
    "Utiliza juegos apropiados para el tamaño y las características de tu mascota.",
    "Revisa periódicamente el estado de los juguetes para retirar aquellos que estén dañados.",
    "Evita ofrecer alimentos humanos sin comprobar previamente si son seguros para tu mascota.",
    "Mantén identificada a tu mascota con los datos de contacto actualizados.",
    "Revisa periódicamente el pelaje de tu mascota para detectar suciedad, heridas o cambios inusuales.",
    "Lleva un registro de las fechas importantes relacionadas con la salud de tu mascota.",
    "Procura que tu mascota realice actividad física adecuada para su edad y condición.",
    "No obligues a tu mascota a interactuar si muestra señales de miedo o incomodidad.",
    "Premia los comportamientos positivos utilizando métodos adecuados y consistentes.",
    "Mantén una rutina diaria que incluya alimentación, descanso, juego y atención.",
    "Revisa las recomendaciones de alimentación indicadas por el veterinario.",
    "Evita realizar cambios repentinos en la comida de tu mascota.",
    "Mantén los productos antiparasitarios bajo las indicaciones de un profesional.",
    "Revisa las fechas de próximas dosis de vacunas y tratamientos.",
    "Limpia regularmente los accesorios que utiliza tu mascota.",
    "Proporciona un espacio cómodo para que tu mascota pueda dormir.",
    "Evita exponer a tu mascota a temperaturas extremadamente altas o bajas.",
    "Durante los días calurosos, asegúrate de que tenga acceso a agua y sombra.",
    "Durante los días fríos, proporciona un espacio protegido y confortable.",
    "Acostumbra a tu mascota poco a poco a nuevas personas, lugares y situaciones.",
    "Dedica tiempo a conocer las señales corporales de tu mascota.",
    "Mantén las puertas y ventanas seguras para evitar escapes accidentales.",
    "Revisa que los collares y arneses tengan un tamaño adecuado.",
    "Mantén los objetos pequeños fuera del alcance de tu mascota.",
    "No dejes cables eléctricos accesibles para mascotas que puedan morderlos.",
    "Supervisa los juegos cuando utilices objetos que puedan romperse fácilmente.",
    "Lava periódicamente las mantas y camas de tu mascota.",
    "Mantén los recipientes de comida protegidos de insectos y humedad.",
    "Evita alimentar a tu mascota con restos de comida sin conocer sus ingredientes.",
    "Consulta al veterinario si notas cambios persistentes en el apetito.",
    "Consulta al veterinario si notas cambios importantes en el consumo de agua.",
    "Presta atención a cambios repentinos en el nivel de actividad.",
    "Mantén un registro de alergias o sensibilidades conocidas de tu mascota.",
    "Guarda los datos de contacto de tu veterinario en un lugar accesible.",
    "Revisa periódicamente los dientes y encías de tu mascota.",
    "Introduce el cepillado dental poco a poco para evitar estrés.",
    "Utiliza productos de higiene diseñados específicamente para mascotas.",
    "Evita utilizar productos humanos sobre la piel de tu mascota sin orientación profesional.",
    "Mantén los productos de baño fuera del alcance de tu mascota.",
    "Después de los paseos, revisa las patas si estuvieron en lugares con suciedad.",
    "Revisa el pelaje después de actividades al aire libre.",
    "Mantén una buena ventilación en el espacio donde permanece tu mascota.",
    "Evita que tu mascota tenga acceso a plantas potencialmente peligrosas.",
    "Investiga si las plantas de tu hogar son seguras para tus mascotas.",
    "Mantén cerrados los recipientes que contengan sustancias químicas.",
    "No permitas que tu mascota tenga acceso a basura doméstica.",
    "Supervisa a tu mascota cuando explore espacios nuevos.",
    "Enseña órdenes básicas utilizando paciencia y constancia.",
    "Evita castigos físicos o métodos que puedan causar miedo.",
    "Refuerza positivamente los comportamientos que deseas fomentar.",
    "Respeta los momentos de descanso de tu mascota.",
    "Proporciona actividades diferentes para evitar el aburrimiento.",
    "Utiliza juguetes de estimulación mental apropiados.",
    "Cambia periódicamente los juguetes disponibles para mantener el interés.",
    "No sobrecargues a tu mascota con demasiadas actividades.",
    "Adapta los ejercicios a la edad y características individuales.",
    "Los animales mayores pueden necesitar rutinas más tranquilas.",
    "Los cachorros y gatitos necesitan atención especial durante su crecimiento.",
    "Consulta al veterinario sobre las necesidades específicas según la edad.",
    "Mantén las revisiones veterinarias dentro de las fechas recomendadas.",
    "Anota cualquier pregunta que quieras realizar durante una consulta veterinaria.",
    "Lleva el historial de vacunas cuando visites al veterinario.",
    "Registra los tratamientos y medicamentos administrados.",
    "Utiliza recordatorios para no olvidar citas importantes.",
    "Mantén actualizado el peso de tu mascota cuando sea necesario.",
    "Evita comparar el peso de tu mascota con el de otros animales.",
    "Cada mascota tiene necesidades particulares.",
    "Observa la forma de caminar de tu mascota para detectar cambios.",
    "Presta atención a cambios en la postura o movilidad.",
    "Revisa periódicamente las orejas de tu mascota.",
    "Consulta al veterinario si observas secreciones o cambios inusuales.",
    "Mantén las zonas de descanso alejadas de lugares con mucho ruido.",
    "Proporciona escondites seguros para mascotas que los necesiten.",
    "Respeta el espacio personal de tu mascota.",
    "Enseña a los niños a interactuar correctamente con los animales.",
    "Supervisa siempre las interacciones entre niños pequeños y mascotas.",
    "No molestes a una mascota mientras está comiendo.",
    "No despiertes bruscamente a una mascota mientras duerme.",
    "Permite que tu mascota se acerque voluntariamente cuando sea posible.",
    "Aprende a reconocer señales de estrés en tu mascota.",
    "Reduce los estímulos si notas que tu mascota está incómoda.",
    "Durante viajes, utiliza sistemas de transporte adecuados.",
    "Nunca dejes una mascota sola dentro de un vehículo cerrado.",
    "Prepara con anticipación los elementos necesarios para un viaje.",
    "Lleva agua durante paseos largos.",
    "Realiza paseos en horarios adecuados cuando haga mucho calor.",
    "Evita superficies demasiado calientes para las patas.",
    "Busca zonas seguras para que tu mascota pueda explorar.",
    "Mantén a tu mascota bajo control durante los paseos.",
    "Comprueba regularmente que el collar tenga la identificación correcta.",
    "Actualiza los datos de contacto cuando cambies de teléfono.",
    "Mantén una fotografía reciente de tu mascota.",
    "Guarda información importante de salud en un lugar seguro.",
    "Registra cualquier tratamiento realizado por el veterinario.",
    "Anota la fecha de desparasitación de tu mascota.",
    "Utiliza recordatorios para las próximas desparasitaciones.",
    "Mantén los productos veterinarios en su envase original.",
    "Revisa las fechas de vencimiento de los productos.",
    "No administres medicamentos humanos a tu mascota por iniciativa propia.",
    "Consulta siempre antes de administrar un medicamento nuevo.",
    "Mantén una alimentación adecuada para las necesidades de tu mascota.",
    "Controla las cantidades de alimento según las recomendaciones recibidas.",
    "Evita premiar constantemente con comida.",
    "Utiliza juegos como una forma adicional de interacción.",
    "Dedica tiempo diario para observar cómo se encuentra tu mascota.",
    "La prevención es una parte importante del cuidado responsable.",
    "Mantén hábitos de higiene constantes.",
    "Limpia inmediatamente los accidentes dentro del hogar.",
    "Utiliza productos seguros para limpiar las zonas de las mascotas.",
    "Mantén sus utensilios separados de los objetos utilizados por las personas.",
    "Lava regularmente los recipientes de comida.",
    "Cambia el agua con frecuencia.",
    "Comprueba que el agua esté limpia antes de ofrecérsela.",
    "Proporciona alimentos en recipientes apropiados.",
    "Evita cambios frecuentes de alimento sin necesidad.",
    "Introduce alimentos nuevos de forma gradual cuando corresponda.",
    "Observa la reacción de tu mascota ante nuevos alimentos.",
    "Mantén un ambiente tranquilo durante las comidas.",
    "Evita competir con tu mascota por su comida.",
    "Enséñale buenos hábitos desde temprana edad.",
    "Sé constante con las reglas del hogar.",
    "Todos los miembros de la familia deberían seguir las mismas reglas.",
    "Premia la calma y los comportamientos adecuados.",
    "Evita gritarle a tu mascota durante el entrenamiento.",
    "Utiliza palabras cortas y claras durante las órdenes.",
    "Practica durante sesiones cortas para mantener la atención.",
    "Termina las sesiones de entrenamiento con una experiencia positiva.",
    "Sé paciente cuando tu mascota esté aprendiendo algo nuevo.",
    "Recuerda que cada animal aprende a su propio ritmo.",
    "Dedica tiempo para fortalecer el vínculo con tu mascota.",
    "El juego puede ser una excelente forma de crear confianza.",
    "Observa qué actividades disfruta más tu mascota.",
    "Respeta las preferencias individuales de cada animal.",
    "Proporciona oportunidades para explorar de forma segura.",
    "Evita ambientes demasiado estresantes cuando sea posible.",
    "Prepara un espacio tranquilo para momentos de descanso.",
    "Mantén una rutina especialmente durante cambios importantes.",
    "Introduce cambios gradualmente cuando sea posible.",
    "Presta atención cuando tu mascota cambie sus hábitos habituales.",
    "Consulta al veterinario ante cambios que persistan.",
    "No ignores señales de dolor o malestar.",
    "Mantén actualizado el historial de salud.",
    "Registra información importante después de cada consulta.",
    "Guarda las fechas de próximas citas.",
    "Utiliza PetMigo para organizar los cuidados importantes.",
    "Revisa regularmente tus recordatorios de PetMigo.",
    "Actualiza la información de tus mascotas cuando sea necesario.",
    "Mantén registrados los datos importantes de cada mascota.",
    "Revisa periódicamente el perfil de cada mascota.",
    "Usa el calendario de PetMigo para organizar las citas.",
    "Anota las actividades importantes para no olvidarlas.",
    "Organiza las tareas de cuidado con anticipación.",
    "Mantén una rutina de cuidado responsable y constante.",
    "Observa, escucha y conoce las necesidades de tu mascota.",
    "Dedica cada día un momento especial para compartir con ella.",
    "El cuidado responsable comienza con pequeñas acciones diarias.",
    "La paciencia es una de las mejores herramientas para educar.",
    "El cariño debe ir acompañado de buenos hábitos de cuidado.",
    "Conocer a tu mascota te ayuda a reconocer cuándo algo cambia.",
    "Una mascota feliz también necesita descanso y tranquilidad.",
    "El bienestar de una mascota incluye salud física y emocional.",
    "La prevención puede ayudarte a detectar problemas oportunamente.",
    "Mantén una comunicación constante con tu veterinario.",
    "Organiza con anticipación las próximas vacunas y controles.",
    "Revisa los datos de contacto de emergencia.",
    "Ten identificado un centro veterinario de confianza.",
    "Prepara una pequeña lista de información importante para emergencias.",
    "Conoce los hábitos normales de tu mascota.",
    "Los cambios en los hábitos pueden ser importantes.",
    "Mantén atención especial durante etapas de crecimiento.",
    "Adapta el hogar a las necesidades de tu mascota.",
    "Retira objetos peligrosos de las zonas donde juega.",
    "Crea espacios donde pueda descansar sin interrupciones.",
    "Mantén seguros balcones, ventanas y escaleras.",
    "Supervisa el acceso a zonas exteriores.",
    "Comprueba periódicamente las condiciones del entorno.",
    "Proporciona juguetes apropiados y seguros.",
    "Revisa los juguetes antes de utilizarlos.",
    "Mantén una buena rutina de actividad.",
    "Combina actividad física con estimulación mental.",
    "Dedica tiempo para enseñar nuevos comportamientos.",
    "Mantén las sesiones de aprendizaje divertidas.",
    "Celebra los pequeños avances de tu mascota.",
    "Evita esperar resultados inmediatos durante el entrenamiento.",
    "Sé constante y paciente.",
    "Cuida a tu mascota todos los días con responsabilidad.",
    "Recuerda que pequeñas acciones pueden marcar una gran diferencia.",
    "Haz de la salud de tu mascota una prioridad.",
    "Mantén organizadas las fechas importantes.",
    "Revisa PetMigo regularmente para mantener todo actualizado.",
    "Disfruta cada momento compartido con tu mascota.",
    "El mejor cuidado combina atención, prevención, paciencia y cariño."
];


$datos_curiosos = [

    "Los gatos pueden dormir aproximadamente entre 12 y 16 horas al día.",
    "Los perros tienen un sentido del olfato mucho más desarrollado que los humanos.",
    "Cada perro tiene una huella nasal única.",
    "Los gatos utilizan sus bigotes para obtener información sobre su entorno.",
    "Los perros pueden utilizar diferentes posiciones de la cola para comunicarse.",
    "Los gatos pueden producir una gran variedad de sonidos para comunicarse.",
    "Los perros pueden aprender a asociar palabras con objetos y acciones.",
    "Los gatos tienen una excelente capacidad para saltar.",
    "Los perros tienen glándulas sudoríparas principalmente en las almohadillas de sus patas.",
    "Los gatos utilizan el ronroneo como una forma de comunicación.",
    "Los perros pueden distinguir diferentes olores incluso cuando están mezclados.",
    "Los gatos tienen una excelente visión en condiciones de poca luz.",
    "Los perros utilizan principalmente el olfato para explorar el mundo.",
    "Los gatos tienen uñas retráctiles en sus patas delanteras.",
    "Los perros pueden reconocer voces familiares.",
    "Los gatos pueden reconocer la voz de las personas con las que conviven.",
    "Los perros utilizan sus orejas como parte de su comunicación corporal.",
    "Los gatos utilizan la posición de sus orejas para expresar diferentes estados.",
    "Los perros pueden aprender rutinas y anticipar determinadas actividades.",
    "Los gatos suelen tener momentos de mayor actividad durante ciertas horas del día.",
    "Los perros tienen diferentes razas con tamaños y características muy variadas.",
    "Los gatos tienen una gran capacidad para mantener el equilibrio.",
    "Los perros pueden mover sus orejas para localizar sonidos.",
    "Los gatos tienen almohadillas en sus patas que les ayudan a caminar silenciosamente.",
    "Los perros tienen una estructura corporal adaptada a diferentes tipos de actividad.",
    "Los gatos pueden utilizar su cola para expresar diferentes emociones.",
    "Los perros pueden establecer fuertes vínculos sociales con las personas.",
    "Los gatos pueden crear vínculos importantes con sus cuidadores.",
    "Los perros tienen una gran capacidad para aprender mediante repetición.",
    "Los gatos pueden aprender rutinas domésticas.",
    "Los perros pueden asociar determinados sonidos con actividades cotidianas.",
    "Los gatos pueden reconocer lugares familiares.",
    "Los perros pueden recordar experiencias asociadas con determinadas situaciones.",
    "Los gatos pueden mostrar preferencias individuales por diferentes juguetes.",
    "Los perros pueden disfrutar diferentes tipos de juegos dependiendo de su personalidad.",
    "Los gatos utilizan el movimiento de sus orejas para detectar sonidos.",
    "Los perros tienen una gran variedad de formas corporales.",
    "Los gatos pueden utilizar diferentes posturas para comunicarse.",
    "Los perros pueden expresar emociones mediante movimientos corporales.",
    "Los gatos pueden dormir en posiciones muy diferentes.",
    "Los perros pueden tener preferencias individuales por determinadas personas.",
    "Los gatos pueden reconocer algunos sonidos relacionados con su rutina.",
    "Los perros pueden aprender señales con las manos.",
    "Los gatos pueden aprender algunos comportamientos mediante repetición.",
    "Los perros pueden distinguir el tono de voz de las personas.",
    "Los gatos pueden utilizar el contacto físico como forma de comunicación.",
    "Los perros pueden mostrar entusiasmo mediante movimientos de todo el cuerpo.",
    "Los gatos pueden mostrar confianza cuando se relajan cerca de una persona.",
    "Los perros pueden aprender rutinas de alimentación.",
    "Los gatos pueden anticipar algunas actividades habituales.",
    "Los perros pueden aprender a responder a diferentes señales.",
    "Los gatos pueden explorar nuevos objetos con mucho interés.",
    "Los perros pueden utilizar diferentes vocalizaciones para comunicarse.",
    "Los gatos pueden utilizar maullidos con diferentes intensidades.",
    "Los perros pueden reconocer patrones de comportamiento de sus cuidadores.",
    "Los gatos pueden reconocer espacios que consideran seguros.",
    "Los perros pueden aprender mediante refuerzo positivo.",
    "Los gatos también pueden aprender mediante asociaciones positivas.",
    "Los perros tienen diferentes necesidades dependiendo de su tamaño.",
    "Los gatos también tienen necesidades diferentes dependiendo de su edad.",
    "Los perros pueden tener niveles de energía muy diferentes.",
    "Los gatos pueden tener personalidades muy distintas entre sí.",
    "Los perros pueden disfrutar actividades de exploración.",
    "Los gatos pueden disfrutar actividades que estimulen sus instintos de caza.",
    "Los perros pueden utilizar su olfato durante los juegos.",
    "Los gatos pueden perseguir objetos pequeños como parte del juego.",
    "Los perros pueden aprender nombres de juguetes.",
    "Los gatos pueden mostrar interés por objetos que se mueven.",
    "Los perros pueden recordar lugares conocidos.",
    "Los gatos pueden recordar rutas dentro de espacios familiares.",
    "Los perros pueden aprender horarios habituales.",
    "Los gatos pueden reconocer sonidos de objetos domésticos.",
    "Los perros pueden reaccionar ante diferentes sonidos dependiendo de su experiencia.",
    "Los gatos pueden utilizar sus bigotes para detectar obstáculos cercanos.",
    "Los perros tienen diferentes formas de comunicarse con las personas.",
    "Los gatos también tienen diferentes formas de comunicarse con las personas.",
    "Los perros pueden aprender señales verbales y visuales.",
    "Los gatos pueden responder a rutinas y señales conocidas.",
    "Los perros pueden reconocer determinadas palabras con entrenamiento.",
    "Los gatos pueden aprender a responder a sus nombres.",
    "Los perros pueden expresar alegría mediante diferentes comportamientos.",
    "Los gatos pueden expresar tranquilidad mediante determinadas posturas.",
    "Los perros pueden utilizar el olfato para identificar otros animales.",
    "Los gatos pueden utilizar el olfato para explorar objetos nuevos.",
    "Los perros pueden aprender comportamientos complejos paso a paso.",
    "Los gatos pueden aprender diferentes rutinas domésticas.",
    "Los perros pueden adaptarse a diferentes ambientes cuando reciben una adaptación adecuada.",
    "Los gatos pueden adaptarse a diferentes espacios cuando se sienten seguros.",
    "Los perros pueden formar relaciones sociales con otros animales.",
    "Los gatos pueden establecer relaciones sociales dependiendo de su personalidad.",
    "Los perros pueden disfrutar juegos de búsqueda.",
    "Los gatos pueden disfrutar juegos de persecución.",
    "Los perros pueden utilizar juguetes para satisfacer necesidades de exploración.",
    "Los gatos pueden utilizar juguetes para estimular comportamientos naturales.",
    "Los perros pueden mostrar curiosidad mediante el olfato.",
    "Los gatos pueden mostrar curiosidad mediante la exploración.",
    "Los perros pueden aprender límites dentro del hogar.",
    "Los gatos pueden aprender qué espacios forman parte de su entorno habitual.",
    "Los perros pueden recordar rutinas diarias.",
    "Los gatos pueden reconocer rutinas relacionadas con la alimentación.",
    "Los perros pueden responder al lenguaje corporal humano.",
    "Los gatos pueden responder a determinados gestos humanos.",
    "Los perros pueden aprender mediante pequeñas sesiones repetidas.",
    "Los gatos también pueden aprender mediante sesiones cortas.",
    "Los perros pueden distinguir diferentes personas por su olor.",
    "Los gatos también utilizan el olfato para reconocer individuos.",
    "Los perros pueden mostrar preferencias por determinados lugares para descansar.",
    "Los gatos pueden elegir lugares elevados para observar su entorno.",
    "Los perros pueden utilizar el lenguaje corporal para pedir atención.",
    "Los gatos pueden utilizar diferentes comportamientos para solicitar atención.",
    "Los perros pueden reconocer sonidos asociados con paseos.",
    "Los gatos pueden reconocer sonidos relacionados con la comida.",
    "Los perros pueden aprender rutinas relacionadas con la hora de dormir.",
    "Los gatos pueden desarrollar rutinas propias dentro del hogar.",
    "Los perros pueden aprender a esperar antes de recibir comida.",
    "Los gatos pueden aprender rutinas relacionadas con sus horarios.",
    "Los perros pueden disfrutar actividades de estimulación mental.",
    "Los gatos pueden disfrutar actividades que impliquen resolver pequeños desafíos.",
    "Los perros pueden aprender mediante juegos.",
    "Los gatos también pueden aprender mediante juegos.",
    "Los perros pueden desarrollar preferencias por determinados juguetes.",
    "Los gatos pueden desarrollar preferencias por diferentes tipos de juguetes.",
    "Los perros pueden reconocer cambios en su ambiente.",
    "Los gatos pueden notar cambios en objetos de su entorno.",
    "Los perros pueden mostrar curiosidad ante olores nuevos.",
    "Los gatos pueden mostrar curiosidad ante objetos desconocidos.",
    "Los perros pueden reconocer lugares donde han estado anteriormente.",
    "Los gatos pueden reconocer lugares familiares.",
    "Los perros pueden recordar algunas experiencias durante largos periodos.",
    "Los gatos también pueden recordar experiencias asociadas a lugares o situaciones.",
    "Los perros pueden aprender diferentes órdenes con práctica constante.",
    "Los gatos pueden aprender diferentes comportamientos con paciencia.",
    "Los perros pueden responder a recompensas durante el aprendizaje.",
    "Los gatos pueden responder positivamente a recompensas adecuadas.",
    "Los perros pueden utilizar sus patas para interactuar con objetos.",
    "Los gatos utilizan sus patas para explorar objetos.",
    "Los perros pueden usar el olfato para seguir rastros.",
    "Los gatos utilizan el olfato para investigar su entorno.",
    "Los perros pueden reconocer el olor de personas conocidas.",
    "Los gatos pueden reconocer olores familiares.",
    "Los perros pueden mostrar emoción al escuchar sonidos conocidos.",
    "Los gatos pueden reaccionar ante sonidos familiares.",
    "Los perros pueden aprender rutinas relacionadas con la salida de casa.",
    "Los gatos pueden reconocer determinadas rutinas de sus cuidadores.",
    "Los perros pueden disfrutar diferentes tipos de actividad física.",
    "Los gatos pueden disfrutar diferentes formas de juego.",
    "Los perros tienen diferentes estilos de comunicación.",
    "Los gatos también tienen diferentes estilos de comunicación.",
    "Los perros pueden utilizar la mirada para comunicarse.",
    "Los gatos pueden utilizar la mirada y el movimiento corporal para comunicarse.",
    "Los perros pueden aprender asociaciones entre palabras y acciones.",
    "Los gatos pueden aprender asociaciones entre sonidos y actividades.",
    "Los perros pueden reconocer patrones en la rutina de sus cuidadores.",
    "Los gatos pueden reconocer patrones dentro de su ambiente.",
    "Los perros pueden aprender nuevas rutinas con tiempo.",
    "Los gatos pueden adaptarse gradualmente a nuevas rutinas.",
    "Los perros pueden mostrar diferentes niveles de energía según su edad.",
    "Los gatos también pueden cambiar sus niveles de actividad según su edad.",
    "Los perros pueden mostrar comportamientos diferentes según su personalidad.",
    "Los gatos pueden tener personalidades muy particulares.",
    "Los perros pueden disfrutar de juegos de olfato.",
    "Los gatos pueden disfrutar de juegos que impliquen perseguir objetos.",
    "Los perros pueden aprender mediante repetición constante.",
    "Los gatos pueden aprender mediante repetición y asociaciones.",
    "Los perros pueden establecer rutinas de descanso.",
    "Los gatos suelen elegir cuidadosamente algunos lugares para descansar.",
    "Los perros pueden utilizar diferentes vocalizaciones.",
    "Los gatos utilizan diferentes vocalizaciones según la situación.",
    "Los perros pueden comunicarse mediante movimientos de las orejas.",
    "Los gatos también utilizan sus orejas para comunicarse.",
    "Los perros pueden comunicarse mediante movimientos de la cola.",
    "Los gatos utilizan la cola para expresar diferentes estados.",
    "Los perros pueden aprender señales visuales.",
    "Los gatos pueden reconocer determinados gestos.",
    "Los perros pueden reconocer rutinas de sus familias.",
    "Los gatos pueden reconocer rutinas de sus hogares.",
    "Los perros pueden mostrar entusiasmo cuando anticipan actividades conocidas.",
    "Los gatos pueden anticipar algunas actividades mediante sonidos familiares.",
    "Los perros pueden aprender comportamientos nuevos durante toda su vida.",
    "Los gatos también pueden aprender nuevos comportamientos con paciencia.",
    "Los perros pueden disfrutar de actividades que estimulen su mente.",
    "Los gatos pueden disfrutar de actividades que estimulen su curiosidad.",
    "Los perros pueden crear fuertes vínculos con sus cuidadores.",
    "Los gatos pueden crear vínculos importantes con las personas.",
    "Los perros pueden reconocer la voz de sus cuidadores.",
    "Los gatos pueden reconocer voces familiares.",
    "Los perros pueden reconocer olores familiares.",
    "Los gatos pueden reconocer olores asociados a lugares conocidos.",
    "Los perros pueden recordar determinadas experiencias.",
    "Los gatos pueden recordar experiencias asociadas a lugares seguros.",
    "Los perros pueden aprender mediante experiencias positivas.",
    "Los gatos también responden a experiencias positivas.",
    "Los perros pueden disfrutar explorando nuevos lugares seguros.",
    "Los gatos pueden disfrutar explorando nuevos espacios cuando se sienten seguros.",
    "Los perros pueden aprender a utilizar diferentes juguetes.",
    "Los gatos pueden desarrollar preferencias por diferentes juguetes.",
    "Los perros pueden mostrar curiosidad utilizando principalmente el olfato.",
    "Los gatos pueden mostrar curiosidad utilizando principalmente la exploración.",
    "Los perros pueden responder a cambios en el tono de voz.",
    "Los gatos pueden responder a cambios en los sonidos del entorno.",
    "Los perros pueden reconocer determinadas rutinas incluso antes de que ocurran.",
    "Los gatos también pueden anticipar algunas rutinas habituales.",
    "Los perros pueden aprender nombres de personas.",
    "Los gatos pueden aprender a reconocer nombres y sonidos habituales.",
    "Los perros pueden disfrutar de juegos de búsqueda.",
    "Los gatos pueden disfrutar de juegos de persecución.",
    "Los perros pueden utilizar sus sentidos para investigar el ambiente.",
    "Los gatos utilizan varios sentidos para explorar su entorno.",
    "Los perros pueden aprender nuevas habilidades con práctica.",
    "Los gatos pueden aprender nuevos comportamientos con paciencia.",
    "Los perros pueden mostrar diferentes formas de afecto.",
    "Los gatos pueden mostrar afecto de diferentes maneras.",
    "Los perros pueden preferir determinados lugares para dormir.",
    "Los gatos suelen buscar lugares donde se sienten seguros.",
    "Los perros pueden desarrollar hábitos individuales.",
    "Los gatos pueden desarrollar hábitos individuales muy marcados.",
    "Los perros pueden reconocer sonidos cotidianos.",
    "Los gatos pueden reconocer sonidos cotidianos del hogar.",
    "Los perros pueden aprender rutinas de paseo.",
    "Los gatos pueden reconocer rutinas relacionadas con la alimentación.",
    "Los perros pueden aprender a responder a diferentes señales.",
    "Los gatos pueden aprender a responder a señales conocidas.",
    "Los perros pueden disfrutar actividades que involucren su olfato.",
    "Los gatos pueden disfrutar actividades que involucren su curiosidad.",
    "Los perros pueden reconocer cambios en las personas que los rodean.",
    "Los gatos pueden reconocer cambios en su ambiente.",
    "Los perros pueden recordar caminos conocidos.",
    "Los gatos pueden recordar lugares donde se sienten cómodos.",
    "Los perros pueden aprender comportamientos mediante asociaciones.",
    "Los gatos también pueden aprender mediante asociaciones.",
    "Los perros pueden adaptarse a nuevas rutinas de manera gradual.",
    "Los gatos pueden adaptarse a cambios cuando reciben tiempo y seguridad.",
    "Los perros pueden aprender durante toda su vida.",
    "Los gatos también pueden aprender durante toda su vida.",
    "Los perros pueden disfrutar de la interacción con sus cuidadores.",
    "Los gatos pueden disfrutar de la interacción cuando se sienten cómodos.",
    "Los perros pueden mostrar señales corporales antes de ladrar.",
    "Los gatos pueden mostrar señales corporales antes de vocalizar.",
    "Los perros pueden usar diferentes sonidos para llamar la atención.",
    "Los gatos pueden usar diferentes sonidos para llamar la atención.",
    "Los perros pueden reconocer algunas emociones humanas mediante señales.",
    "Los gatos pueden responder a diferentes estados del ambiente.",
    "Los perros pueden aprender mediante observación y repetición.",
    "Los gatos pueden aprender mediante observación y experiencia.",
    "Los perros pueden disfrutar de rutinas previsibles.",
    "Los gatos pueden sentirse cómodos cuando cuentan con espacios conocidos.",
    "Los perros pueden utilizar su cuerpo completo para comunicarse.",
    "Los gatos utilizan postura, cola, orejas y sonidos para comunicarse.",
    "Los perros pueden mostrar interés por nuevos olores.",
    "Los gatos pueden mostrar interés por nuevos objetos.",
    "Los perros pueden aprender a reconocer diferentes sonidos.",
    "Los gatos pueden aprender a reconocer diferentes sonidos.",
    "Los perros pueden disfrutar de actividades de exploración.",
    "Los gatos pueden disfrutar de actividades de exploración.",
    "Los perros pueden tener preferencias individuales por diferentes tipos de comida.",
    "Los gatos pueden tener preferencias individuales por diferentes alimentos.",
    "Los perros pueden aprender horarios habituales de comida.",
    "Los gatos pueden anticipar sus horarios de alimentación.",
    "Los perros pueden reconocer señales relacionadas con la hora de dormir.",
    "Los gatos pueden reconocer rutinas relacionadas con el descanso.",
    "Los perros pueden disfrutar de momentos tranquilos.",
    "Los gatos también necesitan momentos tranquilos.",
    "Los perros pueden aprender nuevos juegos.",
    "Los gatos pueden aprender nuevas formas de jugar.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias asociadas con seguridad.",
    "Los perros pueden desarrollar vínculos mediante experiencias compartidas.",
    "Los gatos pueden fortalecer vínculos mediante interacciones positivas.",
    "Los perros pueden responder a la constancia durante el aprendizaje.",
    "Los gatos pueden responder a la paciencia durante el aprendizaje.",
    "Los perros pueden disfrutar de actividades variadas.",
    "Los gatos pueden disfrutar de diferentes formas de enriquecimiento.",
    "Los perros pueden explorar usando principalmente su olfato.",
    "Los gatos pueden explorar utilizando diferentes sentidos.",
    "Los perros pueden aprender a distinguir diferentes señales.",
    "Los gatos pueden aprender a reconocer señales familiares.",
    "Los perros pueden reconocer patrones diarios.",
    "Los gatos pueden reconocer patrones dentro de su entorno.",
    "Los perros pueden desarrollar rutinas individuales.",
    "Los gatos pueden desarrollar rutinas individuales.",
    "Los perros pueden mostrar comportamientos diferentes dependiendo del contexto.",
    "Los gatos también pueden comportarse de manera diferente dependiendo del contexto.",
    "Los perros pueden aprender nuevas habilidades con entrenamiento positivo.",
    "Los gatos pueden aprender nuevos comportamientos con refuerzo positivo.",
    "Los perros pueden beneficiarse de actividades que estimulen diferentes sentidos.",
    "Los gatos pueden beneficiarse de actividades que estimulen diferentes sentidos.",
    "Los perros pueden reconocer a personas conocidas por diferentes señales.",
    "Los gatos pueden reconocer personas conocidas por diferentes señales.",
    "Los perros pueden mostrar curiosidad ante nuevos ambientes.",
    "Los gatos pueden mostrar curiosidad ante nuevos ambientes.",
    "Los perros pueden aprender a adaptarse gradualmente a situaciones nuevas.",
    "Los gatos pueden adaptarse gradualmente cuando reciben un ambiente seguro.",
    "Los perros pueden recordar determinados lugares.",
    "Los gatos pueden recordar determinados lugares.",
    "Los perros pueden asociar lugares con experiencias positivas.",
    "Los gatos pueden asociar lugares con seguridad y comodidad.",
    "Los perros pueden reconocer diferentes rutinas familiares.",
    "Los gatos pueden reconocer diferentes rutinas domésticas.",
    "Los perros pueden aprender mediante experiencias repetidas.",
    "Los gatos pueden aprender mediante experiencias repetidas.",
    "Los perros pueden mostrar afecto mediante diferentes comportamientos.",
    "Los gatos pueden mostrar afecto mediante diferentes comportamientos.",
    "Los perros pueden utilizar el juego para explorar.",
    "Los gatos pueden utilizar el juego para practicar comportamientos naturales.",
    "Los perros pueden aprender mediante pequeñas metas.",
    "Los gatos pueden aprender mediante pequeños pasos.",
    "Los perros pueden disfrutar de la compañía de sus cuidadores.",
    "Los gatos pueden disfrutar de la compañía cuando se sienten seguros.",
    "Los perros pueden reconocer cambios en las rutinas.",
    "Los gatos pueden reconocer cambios en las rutinas.",
    "Los perros pueden aprender a esperar durante determinadas situaciones.",
    "Los gatos pueden aprender rutinas asociadas con diferentes actividades.",
    "Los perros pueden reconocer sonidos relacionados con objetos familiares.",
    "Los gatos pueden reconocer sonidos relacionados con objetos familiares.",
    "Los perros pueden desarrollar preferencias por ciertos lugares.",
    "Los gatos pueden desarrollar preferencias por ciertos lugares.",
    "Los perros pueden mostrar curiosidad ante nuevos estímulos.",
    "Los gatos pueden mostrar curiosidad ante nuevos estímulos.",
    "Los perros pueden aprender durante diferentes etapas de su vida.",
    "Los gatos pueden aprender durante diferentes etapas de su vida.",
    "Los perros pueden fortalecer vínculos mediante actividades compartidas.",
    "Los gatos pueden fortalecer vínculos mediante actividades compartidas.",
    "Los perros pueden disfrutar de actividades que les permitan explorar.",
    "Los gatos pueden disfrutar de actividades que les permitan explorar.",
    "Los perros pueden reconocer patrones de interacción.",
    "Los gatos pueden reconocer patrones de interacción.",
    "Los perros pueden aprender mediante paciencia y repetición.",
    "Los gatos pueden aprender mediante paciencia y repetición.",
    "Los perros pueden responder a diferentes tipos de estímulos.",
    "Los gatos pueden responder a diferentes tipos de estímulos.",
    "Los perros pueden mostrar diferentes estilos de comunicación.",
    "Los gatos pueden mostrar diferentes estilos de comunicación.",
    "Los perros pueden tener preferencias individuales.",
    "Los gatos también tienen preferencias individuales.",
    "Los perros pueden aprender nuevos hábitos.",
    "Los gatos pueden aprender nuevos hábitos.",
    "Los perros pueden reconocer rutinas familiares.",
    "Los gatos pueden reconocer rutinas familiares.",
    "Los perros pueden disfrutar del juego como actividad de aprendizaje.",
    "Los gatos pueden disfrutar del juego como actividad de aprendizaje.",
    "Los perros pueden utilizar el olfato durante actividades de estimulación.",
    "Los gatos pueden utilizar la exploración durante actividades de estimulación.",
    "Los perros pueden recordar determinadas señales.",
    "Los gatos pueden recordar determinadas señales.",
    "Los perros pueden mostrar interés por actividades nuevas.",
    "Los gatos pueden mostrar interés por actividades nuevas.",
    "Los perros pueden adaptarse a diferentes rutinas.",
    "Los gatos pueden adaptarse a diferentes rutinas.",
    "Los perros pueden aprender comportamientos mediante repetición.",
    "Los gatos pueden aprender comportamientos mediante repetición.",
    "Los perros pueden reconocer personas conocidas.",
    "Los gatos pueden reconocer personas conocidas.",
    "Los perros pueden recordar experiencias relacionadas con personas.",
    "Los gatos pueden recordar experiencias relacionadas con lugares.",
    "Los perros pueden utilizar diferentes sonidos para comunicarse.",
    "Los gatos pueden utilizar diferentes sonidos para comunicarse.",
    "Los perros pueden expresar emociones mediante su cuerpo.",
    "Los gatos pueden expresar emociones mediante su cuerpo.",
    "Los perros pueden aprender a responder a señales.",
    "Los gatos pueden aprender a responder a señales.",
    "Los perros pueden disfrutar de juegos de exploración.",
    "Los gatos pueden disfrutar de juegos de exploración.",
    "Los perros pueden mostrar entusiasmo mediante movimientos corporales.",
    "Los gatos pueden mostrar tranquilidad mediante determinadas posturas.",
    "Los perros pueden aprender nuevas rutinas con tiempo.",
    "Los gatos pueden aprender nuevas rutinas con tiempo.",
    "Los perros pueden reconocer horarios habituales.",
    "Los gatos pueden reconocer horarios habituales.",
    "Los perros pueden disfrutar de la atención de sus cuidadores.",
    "Los gatos pueden disfrutar de la atención cuando se sienten cómodos.",
    "Los perros pueden aprender nuevas asociaciones.",
    "Los gatos pueden aprender nuevas asociaciones.",
    "Los perros pueden reconocer objetos conocidos.",
    "Los gatos pueden reconocer objetos conocidos.",
    "Los perros pueden recordar caminos habituales.",
    "Los gatos pueden recordar espacios habituales.",
    "Los perros pueden disfrutar de diferentes tipos de estimulación.",
    "Los gatos pueden disfrutar de diferentes tipos de estimulación.",
    "Los perros pueden aprender a través del juego.",
    "Los gatos pueden aprender a través del juego.",
    "Los perros pueden reconocer cambios en sonidos.",
    "Los gatos pueden reconocer cambios en sonidos.",
    "Los perros pueden desarrollar preferencias por determinados juegos.",
    "Los gatos pueden desarrollar preferencias por determinados juegos.",
    "Los perros pueden aprender comportamientos nuevos.",
    "Los gatos pueden aprender comportamientos nuevos.",
    "Los perros pueden fortalecer sus vínculos mediante experiencias positivas.",
    "Los gatos pueden fortalecer sus vínculos mediante experiencias positivas.",
    "Los perros pueden mostrar confianza mediante determinadas posturas.",
    "Los gatos pueden mostrar confianza mediante determinadas posturas.",
    "Los perros pueden reconocer lugares seguros.",
    "Los gatos pueden reconocer lugares seguros.",
    "Los perros pueden aprender a través de rutinas.",
    "Los gatos pueden aprender a través de rutinas.",
    "Los perros pueden responder a recompensas.",
    "Los gatos pueden responder a recompensas.",
    "Los perros pueden disfrutar de actividades que involucren diferentes sentidos.",
    "Los gatos pueden disfrutar de actividades que involucren diferentes sentidos.",
    "Los perros pueden aprender durante sesiones cortas.",
    "Los gatos pueden aprender durante sesiones cortas.",
    "Los perros pueden reconocer palabras conocidas.",
    "Los gatos pueden reconocer sonidos conocidos.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden mostrar curiosidad mediante la exploración.",
    "Los gatos pueden mostrar curiosidad mediante la exploración.",
    "Los perros pueden desarrollar hábitos saludables con rutinas constantes.",
    "Los gatos pueden desarrollar hábitos saludables con rutinas constantes.",
    "Los perros pueden disfrutar de momentos de descanso.",
    "Los gatos pueden disfrutar de momentos de descanso.",
    "Los perros pueden reconocer señales relacionadas con actividades.",
    "Los gatos pueden reconocer señales relacionadas con actividades.",
    "Los perros pueden aprender mediante asociaciones positivas.",
    "Los gatos pueden aprender mediante asociaciones positivas.",
    "Los perros pueden adaptarse gradualmente a cambios.",
    "Los gatos pueden adaptarse gradualmente a cambios.",
    "Los perros pueden crear vínculos fuertes mediante experiencias compartidas.",
    "Los gatos pueden crear vínculos fuertes mediante experiencias compartidas.",
    "Los perros pueden utilizar el juego para fortalecer vínculos.",
    "Los gatos pueden utilizar el juego para fortalecer vínculos.",
    "Los perros pueden reconocer rutinas familiares.",
    "Los gatos pueden reconocer rutinas familiares.",
    "Los perros pueden aprender diferentes comportamientos.",
    "Los gatos pueden aprender diferentes comportamientos.",
    "Los perros pueden expresar diferentes emociones mediante su comportamiento.",
    "Los gatos pueden expresar diferentes emociones mediante su comportamiento.",
    "Los perros pueden disfrutar explorando olores nuevos.",
    "Los gatos pueden disfrutar explorando objetos nuevos.",
    "Los perros pueden aprender a responder a sonidos.",
    "Los gatos pueden aprender a responder a sonidos.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden desarrollar preferencias individuales.",
    "Los gatos pueden desarrollar preferencias individuales.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden adaptarse a ambientes nuevos de manera gradual.",
    "Los gatos pueden adaptarse a ambientes nuevos de manera gradual.",
    "Los perros pueden disfrutar de actividades variadas.",
    "Los gatos pueden disfrutar de actividades variadas.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden reconocer personas familiares.",
    "Los gatos pueden reconocer personas familiares.",
    "Los perros pueden reconocer olores familiares.",
    "Los gatos pueden reconocer olores familiares.",
    "Los perros pueden reconocer sonidos familiares.",
    "Los gatos pueden reconocer sonidos familiares.",
    "Los perros pueden recordar lugares familiares.",
    "Los gatos pueden recordar lugares familiares.",
    "Los perros pueden aprender mediante experiencias positivas.",
    "Los gatos pueden aprender mediante experiencias positivas.",
    "Los perros pueden mostrar interés por nuevas actividades.",
    "Los gatos pueden mostrar interés por nuevas actividades.",
    "Los perros pueden aprender durante toda su vida.",
    "Los gatos pueden aprender durante toda su vida.",
    "Los perros pueden establecer vínculos mediante confianza.",
    "Los gatos pueden establecer vínculos mediante confianza.",
    "Los perros pueden utilizar diferentes sentidos para explorar.",
    "Los gatos pueden utilizar diferentes sentidos para explorar.",
    "Los perros pueden aprender nuevas habilidades mediante práctica.",
    "Los gatos pueden aprender nuevas habilidades mediante práctica.",
    "Los perros pueden disfrutar de juegos de búsqueda.",
    "Los gatos pueden disfrutar de juegos de persecución.",
    "Los perros pueden mostrar curiosidad ante estímulos nuevos.",
    "Los gatos pueden mostrar curiosidad ante estímulos nuevos.",
    "Los perros pueden reconocer patrones de comportamiento.",
    "Los gatos pueden reconocer patrones de comportamiento.",
    "Los perros pueden aprender mediante interacción.",
    "Los gatos pueden aprender mediante interacción.",
    "Los perros pueden reconocer rutinas de sus cuidadores.",
    "Los gatos pueden reconocer rutinas de sus cuidadores.",
    "Los perros pueden desarrollar diferentes personalidades.",
    "Los gatos pueden desarrollar diferentes personalidades.",
    "Los perros pueden expresar confianza de diferentes maneras.",
    "Los gatos pueden expresar confianza de diferentes maneras.",
    "Los perros pueden mostrar interés mediante movimientos corporales.",
    "Los gatos pueden mostrar interés mediante movimientos corporales.",
    "Los perros pueden aprender a través de experiencias repetidas.",
    "Los gatos pueden aprender a través de experiencias repetidas.",
    "Los perros pueden adaptarse a nuevas situaciones con paciencia.",
    "Los gatos pueden adaptarse a nuevas situaciones con paciencia.",
    "Los perros pueden reconocer ambientes conocidos.",
    "Los gatos pueden reconocer ambientes conocidos.",
    "Los perros pueden recordar rutinas diarias.",
    "Los gatos pueden recordar rutinas diarias.",
    "Los perros pueden disfrutar de actividades de enriquecimiento.",
    "Los gatos pueden disfrutar de actividades de enriquecimiento.",
    "Los perros pueden aprender mediante juegos.",
    "Los gatos pueden aprender mediante juegos.",
    "Los perros pueden desarrollar hábitos.",
    "Los gatos pueden desarrollar hábitos.",
    "Los perros pueden reconocer señales.",
    "Los gatos pueden reconocer señales.",
    "Los perros pueden responder a diferentes estímulos.",
    "Los gatos pueden responder a diferentes estímulos.",
    "Los perros pueden mostrar diferentes comportamientos según el contexto.",
    "Los gatos pueden mostrar diferentes comportamientos según el contexto.",
    "Los perros pueden aprender a través de la observación.",
    "Los gatos pueden aprender a través de la observación.",
    "Los perros pueden recordar personas conocidas.",
    "Los gatos pueden recordar personas conocidas.",
    "Los perros pueden recordar sonidos conocidos.",
    "Los gatos pueden recordar sonidos conocidos.",
    "Los perros pueden reconocer objetos conocidos.",
    "Los gatos pueden reconocer objetos conocidos.",
    "Los perros pueden desarrollar preferencias por diferentes actividades.",
    "Los gatos pueden desarrollar preferencias por diferentes actividades.",
    "Los perros pueden disfrutar de la exploración.",
    "Los gatos pueden disfrutar de la exploración.",
    "Los perros pueden aprender nuevas asociaciones.",
    "Los gatos pueden aprender nuevas asociaciones.",
    "Los perros pueden reconocer cambios en su entorno.",
    "Los gatos pueden reconocer cambios en su entorno.",
    "Los perros pueden mostrar interés por nuevas experiencias.",
    "Los gatos pueden mostrar interés por nuevas experiencias.",
    "Los perros pueden adaptarse gradualmente a cambios.",
    "Los gatos pueden adaptarse gradualmente a cambios.",
    "Los perros pueden aprender durante diferentes etapas.",
    "Los gatos pueden aprender durante diferentes etapas.",
    "Los perros pueden fortalecer vínculos mediante interacción.",
    "Los gatos pueden fortalecer vínculos mediante interacción.",
    "Los perros pueden mostrar diferentes formas de comunicación.",
    "Los gatos pueden mostrar diferentes formas de comunicación.",
    "Los perros pueden aprender mediante paciencia.",
    "Los gatos pueden aprender mediante paciencia.",
    "Los perros pueden reconocer rutinas.",
    "Los gatos pueden reconocer rutinas.",
    "Los perros pueden disfrutar del juego.",
    "Los gatos pueden disfrutar del juego.",
    "Los perros pueden disfrutar de momentos tranquilos.",
    "Los gatos pueden disfrutar de momentos tranquilos.",
    "Los perros pueden desarrollar confianza mediante experiencias positivas.",
    "Los gatos pueden desarrollar confianza mediante experiencias positivas.",
    "Los perros pueden reconocer lugares seguros.",
    "Los gatos pueden reconocer lugares seguros.",
    "Los perros pueden recordar experiencias agradables.",
    "Los gatos pueden recordar experiencias agradables.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden desarrollar hábitos individuales.",
    "Los gatos pueden desarrollar hábitos individuales.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden utilizar el olfato para explorar.",
    "Los gatos pueden utilizar sus sentidos para explorar.",
    "Los perros pueden utilizar el juego como aprendizaje.",
    "Los gatos pueden utilizar el juego como aprendizaje.",
    "Los perros pueden responder a rutinas conocidas.",
    "Los gatos pueden responder a rutinas conocidas.",
    "Los perros pueden aprender señales nuevas.",
    "Los gatos pueden aprender señales nuevas.",
    "Los perros pueden reconocer sonidos familiares.",
    "Los gatos pueden reconocer sonidos familiares.",
    "Los perros pueden reconocer personas conocidas.",
    "Los gatos pueden reconocer personas conocidas.",
    "Los perros pueden recordar lugares conocidos.",
    "Los gatos pueden recordar lugares conocidos.",
    "Los perros pueden aprender comportamientos nuevos.",
    "Los gatos pueden aprender comportamientos nuevos.",
    "Los perros pueden adaptarse con paciencia.",
    "Los gatos pueden adaptarse con paciencia.",
    "Los perros pueden disfrutar actividades variadas.",
    "Los gatos pueden disfrutar actividades variadas.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden crear vínculos mediante confianza.",
    "Los gatos pueden crear vínculos mediante confianza.",
    "Los perros pueden expresar emociones de diferentes formas.",
    "Los gatos pueden expresar emociones de diferentes formas.",
    "Los perros pueden reconocer patrones.",
    "Los gatos pueden reconocer patrones.",
    "Los perros pueden aprender mediante asociaciones.",
    "Los gatos pueden aprender mediante asociaciones.",
    "Los perros pueden reconocer cambios.",
    "Los gatos pueden reconocer cambios.",
    "Los perros pueden explorar nuevos ambientes.",
    "Los gatos pueden explorar nuevos ambientes.",
    "Los perros pueden recordar experiencias.",
    "Los gatos pueden recordar experiencias.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden desarrollar preferencias.",
    "Los gatos pueden desarrollar preferencias.",
    "Los perros pueden mostrar curiosidad ante nuevos estímulos.",
    "Los gatos pueden mostrar curiosidad ante nuevos estímulos.",
    "Los perros pueden aprender mediante interacción.",
    "Los gatos pueden aprender mediante interacción.",
    "Los perros pueden disfrutar actividades de estimulación.",
    "Los gatos pueden disfrutar actividades de estimulación.",
    "Los perros pueden reconocer rutinas de sus familias.",
    "Los gatos pueden reconocer rutinas de sus familias.",
    "Los perros pueden aprender durante toda su vida.",
    "Los gatos pueden aprender durante toda su vida.",
    "Los perros pueden establecer relaciones sociales.",
    "Los gatos pueden establecer relaciones sociales.",
    "Los perros pueden disfrutar de experiencias compartidas.",
    "Los gatos pueden disfrutar de experiencias compartidas.",
    "Los perros pueden reconocer señales corporales.",
    "Los gatos pueden reconocer señales corporales.",
    "Los perros pueden comunicarse mediante sonidos y movimientos.",
    "Los gatos pueden comunicarse mediante sonidos y movimientos.",
    "Los perros pueden mostrar diferentes niveles de energía.",
    "Los gatos pueden mostrar diferentes niveles de actividad.",
    "Los perros pueden tener preferencias individuales.",
    "Los gatos pueden tener preferencias individuales.",
    "Los perros pueden aprender con paciencia y constancia.",
    "Los gatos pueden aprender con paciencia y constancia.",
    "Los perros pueden adaptarse gradualmente.",
    "Los gatos pueden adaptarse gradualmente.",
    "Los perros pueden reconocer ambientes familiares.",
    "Los gatos pueden reconocer ambientes familiares.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden disfrutar del enriquecimiento.",
    "Los gatos pueden disfrutar del enriquecimiento.",
    "Los perros pueden aprender jugando.",
    "Los gatos pueden aprender jugando.",
    "Los perros pueden crear vínculos fuertes.",
    "Los gatos pueden crear vínculos fuertes.",
    "Los perros pueden reconocer a sus cuidadores.",
    "Los gatos pueden reconocer a sus cuidadores.",
    "Los perros pueden mostrar afecto.",
    "Los gatos pueden mostrar afecto.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden reconocer sonidos habituales.",
    "Los gatos pueden reconocer sonidos habituales.",
    "Los perros pueden reconocer olores habituales.",
    "Los gatos pueden reconocer olores habituales.",
    "Los perros pueden reconocer objetos habituales.",
    "Los gatos pueden reconocer objetos habituales.",
    "Los perros pueden explorar mediante el olfato.",
    "Los gatos pueden explorar mediante sus sentidos.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden desarrollar hábitos individuales.",
    "Los gatos pueden desarrollar hábitos individuales.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden reconocer patrones diarios.",
    "Los gatos pueden reconocer patrones diarios.",
    "Los perros pueden disfrutar de juegos de búsqueda.",
    "Los gatos pueden disfrutar de juegos de persecución.",
    "Los perros pueden utilizar el juego para explorar.",
    "Los gatos pueden utilizar el juego para explorar.",
    "Los perros pueden aprender mediante experiencias positivas.",
    "Los gatos pueden aprender mediante experiencias positivas.",
    "Los perros pueden adaptarse a cambios gradualmente.",
    "Los gatos pueden adaptarse a cambios gradualmente.",
    "Los perros pueden recordar lugares conocidos.",
    "Los gatos pueden recordar lugares conocidos.",
    "Los perros pueden recordar personas conocidas.",
    "Los gatos pueden recordar personas conocidas.",
    "Los perros pueden reconocer voces familiares.",
    "Los gatos pueden reconocer voces familiares.",
    "Los perros pueden reconocer olores familiares.",
    "Los gatos pueden reconocer olores familiares.",
    "Los perros pueden reconocer sonidos familiares.",
    "Los gatos pueden reconocer sonidos familiares.",
    "Los perros pueden reconocer rutinas familiares.",
    "Los gatos pueden reconocer rutinas familiares.",
    "Los perros pueden aprender diferentes comportamientos.",
    "Los gatos pueden aprender diferentes comportamientos.",
    "Los perros pueden desarrollar confianza.",
    "Los gatos pueden desarrollar confianza.",
    "Los perros pueden disfrutar de actividades variadas.",
    "Los gatos pueden disfrutar de actividades variadas.",
    "Los perros pueden aprender mediante interacción.",
    "Los gatos pueden aprender mediante interacción.",
    "Los perros pueden expresar emociones mediante su comportamiento.",
    "Los gatos pueden expresar emociones mediante su comportamiento.",
    "Los perros pueden comunicarse mediante el cuerpo.",
    "Los gatos pueden comunicarse mediante el cuerpo.",
    "Los perros pueden comunicarse mediante sonidos.",
    "Los gatos pueden comunicarse mediante sonidos.",
    "Los perros pueden aprender señales visuales.",
    "Los gatos pueden aprender señales visuales.",
    "Los perros pueden reconocer gestos.",
    "Los gatos pueden reconocer gestos.",
    "Los perros pueden reconocer cambios de rutina.",
    "Los gatos pueden reconocer cambios de rutina.",
    "Los perros pueden mostrar interés por nuevas actividades.",
    "Los gatos pueden mostrar interés por nuevas actividades.",
    "Los perros pueden disfrutar de momentos de descanso.",
    "Los gatos pueden disfrutar de momentos de descanso.",
    "Los perros pueden crear asociaciones con lugares.",
    "Los gatos pueden crear asociaciones con lugares.",
    "Los perros pueden aprender mediante experiencias.",
    "Los gatos pueden aprender mediante experiencias.",
    "Los perros pueden desarrollar rutinas.",
    "Los gatos pueden desarrollar rutinas.",
    "Los perros pueden reconocer horarios.",
    "Los gatos pueden reconocer horarios.",
    "Los perros pueden aprender durante toda su vida.",
    "Los gatos pueden aprender durante toda su vida.",
    "Los perros pueden formar vínculos mediante confianza.",
    "Los gatos pueden formar vínculos mediante confianza.",
    "Los perros pueden mostrar preferencias individuales.",
    "Los gatos pueden mostrar preferencias individuales.",
    "Los perros pueden explorar de diferentes maneras.",
    "Los gatos pueden explorar de diferentes maneras.",
    "Los perros pueden utilizar diferentes sentidos.",
    "Los gatos pueden utilizar diferentes sentidos.",
    "Los perros pueden aprender mediante pequeños pasos.",
    "Los gatos pueden aprender mediante pequeños pasos.",
    "Los perros pueden disfrutar de experiencias nuevas.",
    "Los gatos pueden disfrutar de experiencias nuevas.",
    "Los perros pueden adaptarse con apoyo.",
    "Los gatos pueden adaptarse con apoyo.",
    "Los perros pueden recordar experiencias importantes.",
    "Los gatos pueden recordar experiencias importantes.",
    "Los perros pueden aprender nuevas asociaciones.",
    "Los gatos pueden aprender nuevas asociaciones.",
    "Los perros pueden mostrar comportamientos individuales.",
    "Los gatos pueden mostrar comportamientos individuales.",
    "Los perros pueden tener diferentes personalidades.",
    "Los gatos pueden tener diferentes personalidades.",
    "Los perros pueden reconocer a las personas que los cuidan.",
    "Los gatos pueden reconocer a las personas que los cuidan.",
    "Los perros pueden disfrutar de actividades compartidas.",
    "Los gatos pueden disfrutar de actividades compartidas.",
    "Los perros pueden fortalecer vínculos mediante el juego.",
    "Los gatos pueden fortalecer vínculos mediante el juego.",
    "Los perros pueden fortalecer vínculos mediante rutinas.",
    "Los gatos pueden fortalecer vínculos mediante rutinas.",
    "Los perros pueden aprender mediante paciencia.",
    "Los gatos pueden aprender mediante paciencia.",
    "Los perros pueden responder a experiencias positivas.",
    "Los gatos pueden responder a experiencias positivas.",
    "Los perros pueden reconocer ambientes seguros.",
    "Los gatos pueden reconocer ambientes seguros.",
    "Los perros pueden aprender a través de juegos.",
    "Los gatos pueden aprender a través de juegos.",
    "Los perros pueden desarrollar nuevas habilidades.",
    "Los gatos pueden desarrollar nuevas habilidades.",
    "Los perros pueden mostrar interés por estímulos nuevos.",
    "Los gatos pueden mostrar interés por estímulos nuevos.",
    "Los perros pueden reconocer rutinas diarias.",
    "Los gatos pueden reconocer rutinas diarias.",
    "Los perros pueden aprender comportamientos mediante práctica.",
    "Los gatos pueden aprender comportamientos mediante práctica.",
    "Los perros pueden recordar experiencias agradables.",
    "Los gatos pueden recordar experiencias agradables.",
    "Los perros pueden disfrutar del tiempo con sus cuidadores.",
    "Los gatos pueden disfrutar del tiempo con sus cuidadores.",
    "Los perros pueden expresar confianza.",
    "Los gatos pueden expresar confianza.",
    "Los perros pueden reconocer lugares cómodos.",
    "Los gatos pueden reconocer lugares cómodos.",
    "Los perros pueden desarrollar preferencias por juegos.",
    "Los gatos pueden desarrollar preferencias por juegos.",
    "Los perros pueden desarrollar preferencias por lugares.",
    "Los gatos pueden desarrollar preferencias por lugares.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden recordar señales conocidas.",
    "Los gatos pueden recordar señales conocidas.",
    "Los perros pueden reconocer objetos familiares.",
    "Los gatos pueden reconocer objetos familiares.",
    "Los perros pueden reconocer sonidos familiares.",
    "Los gatos pueden reconocer sonidos familiares.",
    "Los perros pueden reconocer olores familiares.",
    "Los gatos pueden reconocer olores familiares.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden aprender mediante observación.",
    "Los gatos pueden aprender mediante observación.",
    "Los perros pueden adaptarse a cambios graduales.",
    "Los gatos pueden adaptarse a cambios graduales.",
    "Los perros pueden disfrutar de la exploración.",
    "Los gatos pueden disfrutar de la exploración.",
    "Los perros pueden utilizar el olfato para investigar.",
    "Los gatos pueden utilizar sus sentidos para investigar.",
    "Los perros pueden aprender durante diferentes edades.",
    "Los gatos pueden aprender durante diferentes edades.",
    "Los perros pueden formar vínculos sociales.",
    "Los gatos pueden formar vínculos sociales.",
    "Los perros pueden reconocer rutinas de alimentación.",
    "Los gatos pueden reconocer rutinas de alimentación.",
    "Los perros pueden reconocer rutinas de descanso.",
    "Los gatos pueden reconocer rutinas de descanso.",
    "Los perros pueden reconocer rutinas de juego.",
    "Los gatos pueden reconocer rutinas de juego.",
    "Los perros pueden reconocer rutinas de paseo.",
    "Los gatos pueden reconocer rutinas de paseo.",
    "Los perros pueden reconocer rutinas de interacción.",
    "Los gatos pueden reconocer rutinas de interacción.",
    "Los perros pueden desarrollar hábitos relacionados con su entorno.",
    "Los gatos pueden desarrollar hábitos relacionados con su entorno.",
    "Los perros pueden aprender a esperar.",
    "Los gatos pueden aprender rutinas de espera.",
    "Los perros pueden responder a diferentes tonos de voz.",
    "Los gatos pueden responder a diferentes tonos de voz.",
    "Los perros pueden reconocer expresiones corporales.",
    "Los gatos pueden reconocer expresiones corporales.",
    "Los perros pueden mostrar diferentes formas de afecto.",
    "Los gatos pueden mostrar diferentes formas de afecto.",
    "Los perros pueden aprender mediante juegos repetitivos.",
    "Los gatos pueden aprender mediante juegos repetitivos.",
    "Los perros pueden disfrutar de desafíos sencillos.",
    "Los gatos pueden disfrutar de desafíos sencillos.",
    "Los perros pueden utilizar sus sentidos durante el juego.",
    "Los gatos pueden utilizar sus sentidos durante el juego.",
    "Los perros pueden recordar experiencias relacionadas con el juego.",
    "Los gatos pueden recordar experiencias relacionadas con el juego.",
    "Los perros pueden reconocer juguetes conocidos.",
    "Los gatos pueden reconocer juguetes conocidos.",
    "Los perros pueden reconocer lugares de descanso.",
    "Los gatos pueden reconocer lugares de descanso.",
    "Los perros pueden mostrar preferencias por determinados espacios.",
    "Los gatos pueden mostrar preferencias por determinados espacios.",
    "Los perros pueden aprender nuevas asociaciones con objetos.",
    "Los gatos pueden aprender nuevas asociaciones con objetos.",
    "Los perros pueden reconocer sonidos relacionados con actividades.",
    "Los gatos pueden reconocer sonidos relacionados con actividades.",
    "Los perros pueden reconocer olores relacionados con lugares.",
    "Los gatos pueden reconocer olores relacionados con lugares.",
    "Los perros pueden recordar experiencias relacionadas con lugares.",
    "Los gatos pueden recordar experiencias relacionadas con lugares.",
    "Los perros pueden reconocer patrones de actividad.",
    "Los gatos pueden reconocer patrones de actividad.",
    "Los perros pueden adaptarse a nuevas experiencias.",
    "Los gatos pueden adaptarse a nuevas experiencias.",
    "Los perros pueden aprender mediante interacción positiva.",
    "Los gatos pueden aprender mediante interacción positiva.",
    "Los perros pueden disfrutar de la atención.",
    "Los gatos pueden disfrutar de la atención.",
    "Los perros pueden reconocer a sus cuidadores.",
    "Los gatos pueden reconocer a sus cuidadores.",
    "Los perros pueden aprender nuevos hábitos.",
    "Los gatos pueden aprender nuevos hábitos.",
    "Los perros pueden recordar rutinas.",
    "Los gatos pueden recordar rutinas.",
    "Los perros pueden mostrar interés por nuevos objetos.",
    "Los gatos pueden mostrar interés por nuevos objetos.",
    "Los perros pueden mostrar interés por nuevos sonidos.",
    "Los gatos pueden mostrar interés por nuevos sonidos.",
    "Los perros pueden mostrar interés por nuevos olores.",
    "Los gatos pueden mostrar interés por nuevos olores.",
    "Los perros pueden explorar diferentes espacios.",
    "Los gatos pueden explorar diferentes espacios.",
    "Los perros pueden desarrollar confianza mediante rutinas.",
    "Los gatos pueden desarrollar confianza mediante rutinas.",
    "Los perros pueden aprender mediante constancia.",
    "Los gatos pueden aprender mediante constancia.",
    "Los perros pueden reconocer señales habituales.",
    "Los gatos pueden reconocer señales habituales.",
    "Los perros pueden disfrutar de juegos conocidos.",
    "Los gatos pueden disfrutar de juegos conocidos.",
    "Los perros pueden aprender nuevos juegos.",
    "Los gatos pueden aprender nuevos juegos.",
    "Los perros pueden crear recuerdos asociados con experiencias positivas.",
    "Los gatos pueden crear recuerdos asociados con experiencias positivas.",
    "Los perros pueden reconocer espacios familiares.",
    "Los gatos pueden reconocer espacios familiares.",
    "Los perros pueden mostrar diferentes formas de comunicación.",
    "Los gatos pueden mostrar diferentes formas de comunicación.",
    "Los perros pueden aprender mediante observación y práctica.",
    "Los gatos pueden aprender mediante observación y práctica.",
    "Los perros pueden adaptarse mediante experiencias graduales.",
    "Los gatos pueden adaptarse mediante experiencias graduales.",
    "Los perros pueden disfrutar de rutinas estables.",
    "Los gatos pueden disfrutar de rutinas estables.",
    "Los perros pueden reconocer señales de sus cuidadores.",
    "Los gatos pueden reconocer señales de sus cuidadores.",
    "Los perros pueden aprender comportamientos mediante refuerzo positivo.",
    "Los gatos pueden aprender comportamientos mediante refuerzo positivo.",
    "Los perros pueden disfrutar de actividades de búsqueda.",
    "Los gatos pueden disfrutar de actividades de exploración.",
    "Los perros pueden recordar actividades favoritas.",
    "Los gatos pueden recordar actividades favoritas.",
    "Los perros pueden mostrar entusiasmo ante rutinas conocidas.",
    "Los gatos pueden mostrar entusiasmo ante rutinas conocidas.",
    "Los perros pueden mostrar tranquilidad en ambientes seguros.",
    "Los gatos pueden mostrar tranquilidad en ambientes seguros.",
    "Los perros pueden reconocer personas familiares.",
    "Los gatos pueden reconocer personas familiares.",
    "Los perros pueden reconocer objetos familiares.",
    "Los gatos pueden reconocer objetos familiares.",
    "Los perros pueden reconocer sonidos familiares.",
    "Los gatos pueden reconocer sonidos familiares.",
    "Los perros pueden reconocer olores familiares.",
    "Los gatos pueden reconocer olores familiares.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden desarrollar nuevas preferencias.",
    "Los gatos pueden desarrollar nuevas preferencias.",
    "Los perros pueden adaptarse a diferentes situaciones.",
    "Los gatos pueden adaptarse a diferentes situaciones.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden fortalecer vínculos con experiencias compartidas.",
    "Los gatos pueden fortalecer vínculos con experiencias compartidas.",
    "Los perros pueden disfrutar del contacto con sus cuidadores.",
    "Los gatos pueden disfrutar del contacto cuando lo desean.",
    "Los perros pueden expresar diferentes emociones.",
    "Los gatos pueden expresar diferentes emociones.",
    "Los perros pueden utilizar diferentes señales para comunicarse.",
    "Los gatos pueden utilizar diferentes señales para comunicarse.",
    "Los perros pueden reconocer patrones de comportamiento.",
    "Los gatos pueden reconocer patrones de comportamiento.",
    "Los perros pueden aprender mediante pequeñas prácticas.",
    "Los gatos pueden aprender mediante pequeñas prácticas.",
    "Los perros pueden disfrutar de experiencias positivas.",
    "Los gatos pueden disfrutar de experiencias positivas.",
    "Los perros pueden desarrollar hábitos mediante rutinas.",
    "Los gatos pueden desarrollar hábitos mediante rutinas.",
    "Los perros pueden reconocer espacios conocidos.",
    "Los gatos pueden reconocer espacios conocidos.",
    "Los perros pueden recordar personas conocidas.",
    "Los gatos pueden recordar personas conocidas.",
    "Los perros pueden recordar sonidos conocidos.",
    "Los gatos pueden recordar sonidos conocidos.",
    "Los perros pueden recordar olores conocidos.",
    "Los gatos pueden recordar olores conocidos.",
    "Los perros pueden reconocer diferentes actividades.",
    "Los gatos pueden reconocer diferentes actividades.",
    "Los perros pueden anticipar algunas rutinas.",
    "Los gatos pueden anticipar algunas rutinas.",
    "Los perros pueden aprender a través del juego.",
    "Los gatos pueden aprender a través del juego.",
    "Los perros pueden utilizar sus sentidos para aprender.",
    "Los gatos pueden utilizar sus sentidos para aprender.",
    "Los perros pueden desarrollar confianza mediante experiencias positivas.",
    "Los gatos pueden desarrollar confianza mediante experiencias positivas.",
    "Los perros pueden disfrutar de actividades nuevas.",
    "Los gatos pueden disfrutar de actividades nuevas.",
    "Los perros pueden reconocer señales de seguridad.",
    "Los gatos pueden reconocer señales de seguridad.",
    "Los perros pueden aprender con paciencia.",
    "Los gatos pueden aprender con paciencia.",
    "Los perros pueden formar vínculos mediante rutinas.",
    "Los gatos pueden formar vínculos mediante rutinas.",
    "Los perros pueden disfrutar de momentos compartidos.",
    "Los gatos pueden disfrutar de momentos compartidos.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden reconocer lugares donde se sienten cómodos.",
    "Los gatos pueden reconocer lugares donde se sienten cómodos.",
    "Los perros pueden desarrollar preferencias por rutinas.",
    "Los gatos pueden desarrollar preferencias por rutinas.",
    "Los perros pueden reconocer actividades habituales.",
    "Los gatos pueden reconocer actividades habituales.",
    "Los perros pueden aprender diferentes señales.",
    "Los gatos pueden aprender diferentes señales.",
    "Los perros pueden desarrollar diferentes formas de comunicación.",
    "Los gatos pueden desarrollar diferentes formas de comunicación.",
    "Los perros pueden mostrar curiosidad ante experiencias nuevas.",
    "Los gatos pueden mostrar curiosidad ante experiencias nuevas.",
    "Los perros pueden aprender mediante experiencias positivas.",
    "Los gatos pueden aprender mediante experiencias positivas.",
    "Los perros pueden adaptarse con tiempo.",
    "Los gatos pueden adaptarse con tiempo.",
    "Los perros pueden disfrutar de juegos variados.",
    "Los gatos pueden disfrutar de juegos variados.",
    "Los perros pueden recordar sus rutinas.",
    "Los gatos pueden recordar sus rutinas.",
    "Los perros pueden reconocer las rutinas de su familia.",
    "Los gatos pueden reconocer las rutinas de su familia.",
    "Los perros pueden aprender a través de la interacción.",
    "Los gatos pueden aprender a través de la interacción.",
    "Los perros pueden desarrollar vínculos fuertes.",
    "Los gatos pueden desarrollar vínculos fuertes.",
    "Los perros pueden mostrar confianza mediante su comportamiento.",
    "Los gatos pueden mostrar confianza mediante su comportamiento.",
    "Los perros pueden expresar bienestar mediante comportamientos tranquilos.",
    "Los gatos pueden expresar bienestar mediante comportamientos tranquilos.",
    "Los perros pueden disfrutar de espacios seguros.",
    "Los gatos pueden disfrutar de espacios seguros.",
    "Los perros pueden reconocer ambientes conocidos.",
    "Los gatos pueden reconocer ambientes conocidos.",
    "Los perros pueden aprender mediante constancia.",
    "Los gatos pueden aprender mediante constancia.",
    "Los perros pueden disfrutar de actividades de estimulación.",
    "Los gatos pueden disfrutar de actividades de estimulación.",
    "Los perros pueden recordar experiencias relacionadas con sus cuidadores.",
    "Los gatos pueden recordar experiencias relacionadas con sus cuidadores.",
    "Los perros pueden reconocer señales de sus rutinas.",
    "Los gatos pueden reconocer señales de sus rutinas.",
    "Los perros pueden aprender nuevas asociaciones.",
    "Los gatos pueden aprender nuevas asociaciones.",
    "Los perros pueden adaptarse gradualmente a cambios.",
    "Los gatos pueden adaptarse gradualmente a cambios.",
    "Los perros pueden desarrollar preferencias por diferentes actividades.",
    "Los gatos pueden desarrollar preferencias por diferentes actividades.",
    "Los perros pueden explorar utilizando diferentes sentidos.",
    "Los gatos pueden explorar utilizando diferentes sentidos.",
    "Los perros pueden aprender mediante experiencias repetidas.",
    "Los gatos pueden aprender mediante experiencias repetidas.",
    "Los perros pueden reconocer patrones diarios.",
    "Los gatos pueden reconocer patrones diarios.",
    "Los perros pueden disfrutar de rutinas conocidas.",
    "Los gatos pueden disfrutar de rutinas conocidas.",
    "Los perros pueden desarrollar hábitos saludables mediante rutinas.",
    "Los gatos pueden desarrollar hábitos saludables mediante rutinas.",
    "Los perros pueden aprender comportamientos nuevos.",
    "Los gatos pueden aprender comportamientos nuevos.",
    "Los perros pueden mostrar interés por actividades nuevas.",
    "Los gatos pueden mostrar interés por actividades nuevas.",
    "Los perros pueden disfrutar de experiencias compartidas.",
    "Los gatos pueden disfrutar de experiencias compartidas.",
    "Los perros pueden crear vínculos mediante experiencias positivas.",
    "Los gatos pueden crear vínculos mediante experiencias positivas.",
    "Los perros pueden reconocer personas conocidas por su voz.",
    "Los gatos pueden reconocer personas conocidas por su voz.",
    "Los perros pueden reconocer lugares por sus olores.",
    "Los gatos pueden reconocer lugares por sus olores.",
    "Los perros pueden aprender a reconocer objetos.",
    "Los gatos pueden aprender a reconocer objetos.",
    "Los perros pueden recordar actividades habituales.",
    "Los gatos pueden recordar actividades habituales.",
    "Los perros pueden anticipar algunas actividades.",
    "Los gatos pueden anticipar algunas actividades.",
    "Los perros pueden aprender mediante juegos.",
    "Los gatos pueden aprender mediante juegos.",
    "Los perros pueden disfrutar del tiempo de descanso.",
    "Los gatos pueden disfrutar del tiempo de descanso.",
    "Los perros pueden mostrar preferencias por lugares de descanso.",
    "Los gatos pueden mostrar preferencias por lugares de descanso.",
    "Los perros pueden mostrar preferencias por juguetes.",
    "Los gatos pueden mostrar preferencias por juguetes.",
    "Los perros pueden aprender mediante práctica.",
    "Los gatos pueden aprender mediante práctica.",
    "Los perros pueden reconocer diferentes voces.",
    "Los gatos pueden reconocer diferentes voces.",
    "Los perros pueden reconocer diferentes olores.",
    "Los gatos pueden reconocer diferentes olores.",
    "Los perros pueden reconocer diferentes sonidos.",
    "Los gatos pueden reconocer diferentes sonidos.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden desarrollar confianza.",
    "Los gatos pueden desarrollar confianza.",
    "Los perros pueden aprender a través de rutinas.",
    "Los gatos pueden aprender a través de rutinas.",
    "Los perros pueden adaptarse a diferentes ambientes.",
    "Los gatos pueden adaptarse a diferentes ambientes.",
    "Los perros pueden aprender mediante refuerzo positivo.",
    "Los gatos pueden aprender mediante refuerzo positivo.",
    "Los perros pueden desarrollar nuevas habilidades.",
    "Los gatos pueden desarrollar nuevas habilidades.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden disfrutar de la exploración.",
    "Los gatos pueden disfrutar de la exploración.",
    "Los perros pueden utilizar el olfato para investigar.",
    "Los gatos pueden utilizar sus sentidos para investigar.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden reconocer señales conocidas.",
    "Los gatos pueden reconocer señales conocidas.",
    "Los perros pueden reconocer rutinas familiares.",
    "Los gatos pueden reconocer rutinas familiares.",
    "Los perros pueden crear asociaciones con experiencias.",
    "Los gatos pueden crear asociaciones con experiencias.",
    "Los perros pueden aprender comportamientos mediante práctica.",
    "Los gatos pueden aprender comportamientos mediante práctica.",
    "Los perros pueden recordar personas conocidas.",
    "Los gatos pueden recordar personas conocidas.",
    "Los perros pueden recordar lugares conocidos.",
    "Los gatos pueden recordar lugares conocidos.",
    "Los perros pueden recordar sonidos conocidos.",
    "Los gatos pueden recordar sonidos conocidos.",
    "Los perros pueden reconocer olores conocidos.",
    "Los gatos pueden reconocer olores conocidos.",
    "Los perros pueden disfrutar actividades de búsqueda.",
    "Los gatos pueden disfrutar actividades de exploración.",
    "Los perros pueden desarrollar preferencias individuales.",
    "Los gatos pueden desarrollar preferencias individuales.",
    "Los perros pueden aprender mediante constancia.",
    "Los gatos pueden aprender mediante constancia.",
    "Los perros pueden adaptarse con paciencia.",
    "Los gatos pueden adaptarse con paciencia.",
    "Los perros pueden fortalecer vínculos mediante juego.",
    "Los gatos pueden fortalecer vínculos mediante juego.",
    "Los perros pueden fortalecer vínculos mediante rutinas.",
    "Los gatos pueden fortalecer vínculos mediante rutinas.",
    "Los perros pueden reconocer a sus cuidadores.",
    "Los gatos pueden reconocer a sus cuidadores.",
    "Los perros pueden mostrar afecto de diferentes maneras.",
    "Los gatos pueden mostrar afecto de diferentes maneras.",
    "Los perros pueden expresar tranquilidad mediante determinadas posturas.",
    "Los gatos pueden expresar tranquilidad mediante determinadas posturas.",
    "Los perros pueden reconocer ambientes seguros.",
    "Los gatos pueden reconocer ambientes seguros.",
    "Los perros pueden aprender mediante experiencias positivas.",
    "Los gatos pueden aprender mediante experiencias positivas.",
    "Los perros pueden disfrutar de actividades compartidas.",
    "Los gatos pueden disfrutar de actividades compartidas.",
    "Los perros pueden desarrollar hábitos.",
    "Los gatos pueden desarrollar hábitos.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden reconocer cambios.",
    "Los gatos pueden reconocer cambios.",
    "Los perros pueden explorar nuevos lugares.",
    "Los gatos pueden explorar nuevos lugares.",
    "Los perros pueden recordar experiencias.",
    "Los gatos pueden recordar experiencias.",
    "Los perros pueden reconocer rutinas.",
    "Los gatos pueden reconocer rutinas.",
    "Los perros pueden adaptarse gradualmente.",
    "Los gatos pueden adaptarse gradualmente.",
    "Los perros pueden aprender mediante interacción.",
    "Los gatos pueden aprender mediante interacción.",
    "Los perros pueden disfrutar del juego.",
    "Los gatos pueden disfrutar del juego.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden reconocer señales.",
    "Los gatos pueden reconocer señales.",
    "Los perros pueden desarrollar preferencias.",
    "Los gatos pueden desarrollar preferencias.",
    "Los perros pueden formar vínculos.",
    "Los gatos pueden formar vínculos.",
    "Los perros pueden aprender mediante paciencia.",
    "Los gatos pueden aprender mediante paciencia.",
    "Los perros pueden reconocer sonidos.",
    "Los gatos pueden reconocer sonidos.",
    "Los perros pueden reconocer olores.",
    "Los gatos pueden reconocer olores.",
    "Los perros pueden reconocer lugares.",
    "Los gatos pueden reconocer lugares.",
    "Los perros pueden recordar personas.",
    "Los gatos pueden recordar personas.",
    "Los perros pueden aprender rutinas.",
    "Los gatos pueden aprender rutinas.",
    "Los perros pueden desarrollar confianza.",
    "Los gatos pueden desarrollar confianza.",
    "Los perros pueden disfrutar de actividades variadas.",
    "Los gatos pueden disfrutar de actividades variadas.",
    "Los perros pueden aprender mediante juegos.",
    "Los gatos pueden aprender mediante juegos.",
    "Los perros pueden reconocer patrones.",
    "Los gatos pueden reconocer patrones.",
    "Los perros pueden adaptarse a nuevas situaciones.",
    "Los gatos pueden adaptarse a nuevas situaciones.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden disfrutar de experiencias nuevas.",
    "Los gatos pueden disfrutar de experiencias nuevas.",
    "Los perros pueden desarrollar hábitos individuales.",
    "Los gatos pueden desarrollar hábitos individuales.",
    "Los perros pueden reconocer actividades habituales.",
    "Los gatos pueden reconocer actividades habituales.",
    "Los perros pueden aprender comportamientos nuevos.",
    "Los gatos pueden aprender comportamientos nuevos.",
    "Los perros pueden reconocer señales de sus cuidadores.",
    "Los gatos pueden reconocer señales de sus cuidadores.",
    "Los perros pueden fortalecer vínculos mediante interacción.",
    "Los gatos pueden fortalecer vínculos mediante interacción.",
    "Los perros pueden disfrutar de momentos compartidos.",
    "Los gatos pueden disfrutar de momentos compartidos.",
    "Los perros pueden expresar diferentes emociones.",
    "Los gatos pueden expresar diferentes emociones.",
    "Los perros pueden utilizar diferentes sentidos.",
    "Los gatos pueden utilizar diferentes sentidos.",
    "Los perros pueden aprender durante toda su vida.",
    "Los gatos pueden aprender durante toda su vida.",
    "Los perros pueden adaptarse con tiempo.",
    "Los gatos pueden adaptarse con tiempo.",
    "Los perros pueden reconocer espacios familiares.",
    "Los gatos pueden reconocer espacios familiares.",
    "Los perros pueden recordar rutinas.",
    "Los gatos pueden recordar rutinas.",
    "Los perros pueden disfrutar de espacios seguros.",
    "Los gatos pueden disfrutar de espacios seguros.",
    "Los perros pueden aprender mediante práctica.",
    "Los gatos pueden aprender mediante práctica.",
    "Los perros pueden mostrar interés por estímulos nuevos.",
    "Los gatos pueden mostrar interés por estímulos nuevos.",
    "Los perros pueden reconocer personas familiares.",
    "Los gatos pueden reconocer personas familiares.",
    "Los perros pueden reconocer objetos familiares.",
    "Los gatos pueden reconocer objetos familiares.",
    "Los perros pueden reconocer sonidos familiares.",
    "Los gatos pueden reconocer sonidos familiares.",
    "Los perros pueden reconocer olores familiares.",
    "Los gatos pueden reconocer olores familiares.",
    "Los perros pueden aprender nuevas asociaciones.",
    "Los gatos pueden aprender nuevas asociaciones.",
    "Los perros pueden desarrollar preferencias por actividades.",
    "Los gatos pueden desarrollar preferencias por actividades.",
    "Los perros pueden desarrollar preferencias por lugares.",
    "Los gatos pueden desarrollar preferencias por lugares.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden disfrutar de juegos.",
    "Los gatos pueden disfrutar de juegos.",
    "Los perros pueden mostrar confianza.",
    "Los gatos pueden mostrar confianza.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden reconocer rutinas.",
    "Los gatos pueden reconocer rutinas.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden formar vínculos.",
    "Los gatos pueden formar vínculos.",
    "Los perros pueden disfrutar de experiencias positivas.",
    "Los gatos pueden disfrutar de experiencias positivas.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden adaptarse gradualmente.",
    "Los gatos pueden adaptarse gradualmente.",
    "Los perros pueden aprender mediante experiencias.",
    "Los gatos pueden aprender mediante experiencias.",
    "Los perros pueden reconocer ambientes.",
    "Los gatos pueden reconocer ambientes.",
    "Los perros pueden explorar.",
    "Los gatos pueden explorar.",
    "Los perros pueden aprender.",
    "Los gatos pueden aprender.",
    "Los perros pueden desarrollar hábitos.",
    "Los gatos pueden desarrollar hábitos.",
    "Los perros pueden crear vínculos.",
    "Los gatos pueden crear vínculos.",
    "Los perros pueden reconocer a sus cuidadores.",
    "Los gatos pueden reconocer a sus cuidadores.",
    "Los perros pueden recordar personas conocidas.",
    "Los gatos pueden recordar personas conocidas.",
    "Los perros pueden reconocer lugares conocidos.",
    "Los gatos pueden reconocer lugares conocidos.",
    "Los perros pueden recordar sonidos conocidos.",
    "Los gatos pueden recordar sonidos conocidos.",
    "Los perros pueden reconocer olores conocidos.",
    "Los gatos pueden reconocer olores conocidos.",
    "Los perros pueden aprender rutinas.",
    "Los gatos pueden aprender rutinas.",
    "Los perros pueden adaptarse.",
    "Los gatos pueden adaptarse.",
    "Los perros pueden aprender mediante juegos.",
    "Los gatos pueden aprender mediante juegos.",
    "Los perros pueden disfrutar del juego.",
    "Los gatos pueden disfrutar del juego.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden reconocer patrones.",
    "Los gatos pueden reconocer patrones.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden desarrollar confianza.",
    "Los gatos pueden desarrollar confianza.",
    "Los perros pueden mostrar afecto.",
    "Los gatos pueden mostrar afecto.",
    "Los perros pueden disfrutar de actividades compartidas.",
    "Los gatos pueden disfrutar de actividades compartidas.",
    "Los perros pueden reconocer señales.",
    "Los gatos pueden reconocer señales.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden desarrollar preferencias.",
    "Los gatos pueden desarrollar preferencias.",
    "Los perros pueden recordar experiencias.",
    "Los gatos pueden recordar experiencias.",
    "Los perros pueden reconocer rutinas.",
    "Los gatos pueden reconocer rutinas.",
    "Los perros pueden adaptarse a cambios.",
    "Los gatos pueden adaptarse a cambios.",
    "Los perros pueden explorar nuevos espacios.",
    "Los gatos pueden explorar nuevos espacios.",
    "Los perros pueden aprender mediante interacción.",
    "Los gatos pueden aprender mediante interacción.",
    "Los perros pueden fortalecer vínculos.",
    "Los gatos pueden fortalecer vínculos.",
    "Los perros pueden disfrutar momentos tranquilos.",
    "Los gatos pueden disfrutar momentos tranquilos.",
    "Los perros pueden reconocer espacios seguros.",
    "Los gatos pueden reconocer espacios seguros.",
    "Los perros pueden aprender mediante paciencia.",
    "Los gatos pueden aprender mediante paciencia.",
    "Los perros pueden desarrollar hábitos individuales.",
    "Los gatos pueden desarrollar hábitos individuales.",
    "Los perros pueden reconocer actividades habituales.",
    "Los gatos pueden reconocer actividades habituales.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden recordar lugares.",
    "Los gatos pueden recordar lugares.",
    "Los perros pueden recordar personas.",
    "Los gatos pueden recordar personas.",
    "Los perros pueden recordar sonidos.",
    "Los gatos pueden recordar sonidos.",
    "Los perros pueden recordar olores.",
    "Los gatos pueden recordar olores.",
    "Los perros pueden reconocer objetos.",
    "Los gatos pueden reconocer objetos.",
    "Los perros pueden reconocer actividades.",
    "Los gatos pueden reconocer actividades.",
    "Los perros pueden reconocer cambios.",
    "Los gatos pueden reconocer cambios.",
    "Los perros pueden aprender comportamientos.",
    "Los gatos pueden aprender comportamientos.",
    "Los perros pueden desarrollar confianza.",
    "Los gatos pueden desarrollar confianza.",
    "Los perros pueden expresar emociones.",
    "Los gatos pueden expresar emociones.",
    "Los perros pueden comunicarse mediante sonidos.",
    "Los gatos pueden comunicarse mediante sonidos.",
    "Los perros pueden comunicarse mediante movimientos.",
    "Los gatos pueden comunicarse mediante movimientos.",
    "Los perros pueden aprender señales.",
    "Los gatos pueden aprender señales.",
    "Los perros pueden reconocer voces.",
    "Los gatos pueden reconocer voces.",
    "Los perros pueden reconocer olores.",
    "Los gatos pueden reconocer olores.",
    "Los perros pueden reconocer sonidos.",
    "Los gatos pueden reconocer sonidos.",
    "Los perros pueden aprender mediante observación.",
    "Los gatos pueden aprender mediante observación.",
    "Los perros pueden aprender mediante práctica.",
    "Los gatos pueden aprender mediante práctica.",
    "Los perros pueden disfrutar de la exploración.",
    "Los gatos pueden disfrutar de la exploración.",
    "Los perros pueden mostrar interés.",
    "Los gatos pueden mostrar interés.",
    "Los perros pueden desarrollar preferencias.",
    "Los gatos pueden desarrollar preferencias.",
    "Los perros pueden recordar experiencias.",
    "Los gatos pueden recordar experiencias.",
    "Los perros pueden adaptarse.",
    "Los gatos pueden adaptarse.",
    "Los perros pueden aprender.",
    "Los gatos pueden aprender.",
    "Los perros pueden crear vínculos.",
    "Los gatos pueden crear vínculos.",
    "Los perros pueden disfrutar de experiencias positivas.",
    "Los gatos pueden disfrutar de experiencias positivas.",
    "Los perros pueden reconocer ambientes familiares.",
    "Los gatos pueden reconocer ambientes familiares.",
    "Los perros pueden reconocer personas familiares.",
    "Los gatos pueden reconocer personas familiares.",
    "Los perros pueden recordar rutinas.",
    "Los gatos pueden recordar rutinas.",
    "Los perros pueden reconocer patrones diarios.",
    "Los gatos pueden reconocer patrones diarios.",
    "Los perros pueden aprender nuevas asociaciones.",
    "Los gatos pueden aprender nuevas asociaciones.",
    "Los perros pueden disfrutar actividades nuevas.",
    "Los gatos pueden disfrutar actividades nuevas.",
    "Los perros pueden aprender mediante juegos.",
    "Los gatos pueden aprender mediante juegos.",
    "Los perros pueden mostrar confianza.",
    "Los gatos pueden mostrar confianza.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden reconocer señales.",
    "Los gatos pueden reconocer señales.",
    "Los perros pueden desarrollar hábitos.",
    "Los gatos pueden desarrollar hábitos.",
    "Los perros pueden formar vínculos.",
    "Los gatos pueden formar vínculos.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden adaptarse gradualmente.",
    "Los gatos pueden adaptarse gradualmente.",
    "Los perros pueden aprender mediante paciencia.",
    "Los gatos pueden aprender mediante paciencia.",
    "Los perros pueden disfrutar de actividades compartidas.",
    "Los gatos pueden disfrutar de actividades compartidas.",
    "Los perros pueden reconocer rutinas familiares.",
    "Los gatos pueden reconocer rutinas familiares.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden desarrollar confianza mediante experiencias.",
    "Los gatos pueden desarrollar confianza mediante experiencias.",
    "Los perros pueden explorar nuevos espacios.",
    "Los gatos pueden explorar nuevos espacios.",
    "Los perros pueden reconocer lugares seguros.",
    "Los gatos pueden reconocer lugares seguros.",
    "Los perros pueden aprender comportamientos nuevos.",
    "Los gatos pueden aprender comportamientos nuevos.",
    "Los perros pueden recordar personas conocidas.",
    "Los gatos pueden recordar personas conocidas.",
    "Los perros pueden reconocer sonidos conocidos.",
    "Los gatos pueden reconocer sonidos conocidos.",
    "Los perros pueden reconocer olores conocidos.",
    "Los gatos pueden reconocer olores conocidos.",
    "Los perros pueden disfrutar de juegos.",
    "Los gatos pueden disfrutar de juegos.",
    "Los perros pueden desarrollar preferencias.",
    "Los gatos pueden desarrollar preferencias.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden adaptarse a situaciones nuevas.",
    "Los gatos pueden adaptarse a situaciones nuevas.",
    "Los perros pueden fortalecer vínculos.",
    "Los gatos pueden fortalecer vínculos.",
    "Los perros pueden mostrar afecto.",
    "Los gatos pueden mostrar afecto.",
    "Los perros pueden expresar tranquilidad.",
    "Los gatos pueden expresar tranquilidad.",
    "Los perros pueden reconocer ambientes conocidos.",
    "Los gatos pueden reconocer ambientes conocidos.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden aprender mediante experiencias.",
    "Los gatos pueden aprender mediante experiencias.",
    "Los perros pueden disfrutar de actividades variadas.",
    "Los gatos pueden disfrutar de actividades variadas.",
    "Los perros pueden recordar actividades.",
    "Los gatos pueden recordar actividades.",
    "Los perros pueden reconocer actividades.",
    "Los gatos pueden reconocer actividades.",
    "Los perros pueden aprender señales.",
    "Los gatos pueden aprender señales.",
    "Los perros pueden reconocer patrones.",
    "Los gatos pueden reconocer patrones.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden explorar.",
    "Los gatos pueden explorar.",
    "Los perros pueden aprender.",
    "Los gatos pueden aprender.",
    "Los perros pueden crear vínculos.",
    "Los gatos pueden crear vínculos.",
    "Los perros pueden desarrollar confianza.",
    "Los gatos pueden desarrollar confianza.",
    "Los perros pueden recordar experiencias.",
    "Los gatos pueden recordar experiencias.",
    "Los perros pueden reconocer lugares.",
    "Los gatos pueden reconocer lugares.",
    "Los perros pueden reconocer personas.",
    "Los gatos pueden reconocer personas.",
    "Los perros pueden reconocer sonidos.",
    "Los gatos pueden reconocer sonidos.",
    "Los perros pueden reconocer olores.",
    "Los gatos pueden reconocer olores.",
    "Los perros pueden reconocer rutinas.",
    "Los gatos pueden reconocer rutinas.",
    "Los perros pueden aprender rutinas.",
    "Los gatos pueden aprender rutinas.",
    "Los perros pueden adaptarse.",
    "Los gatos pueden adaptarse.",
    "Los perros pueden disfrutar.",
    "Los gatos pueden disfrutar.",
    "Los perros pueden mostrar interés.",
    "Los gatos pueden mostrar interés.",
    "Los perros pueden desarrollar preferencias.",
    "Los gatos pueden desarrollar preferencias.",
    "Los perros pueden aprender habilidades.",
    "Los gatos pueden aprender habilidades.",
    "Los perros pueden recordar señales.",
    "Los gatos pueden recordar señales.",
    "Los perros pueden reconocer objetos.",
    "Los gatos pueden reconocer objetos.",
    "Los perros pueden explorar ambientes.",
    "Los gatos pueden explorar ambientes.",
    "Los perros pueden aprender comportamientos.",
    "Los gatos pueden aprender comportamientos.",
    "Los perros pueden fortalecer vínculos.",
    "Los gatos pueden fortalecer vínculos.",
    "Los perros pueden mostrar afecto.",
    "Los gatos pueden mostrar afecto.",
    "Los perros pueden expresar emociones.",
    "Los gatos pueden expresar emociones.",
    "Los perros pueden aprender mediante juego.",
    "Los gatos pueden aprender mediante juego.",
    "Los perros pueden reconocer cambios.",
    "Los gatos pueden reconocer cambios.",
    "Los perros pueden adaptarse gradualmente.",
    "Los gatos pueden adaptarse gradualmente.",
    "Los perros pueden disfrutar experiencias nuevas.",
    "Los gatos pueden disfrutar experiencias nuevas.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden reconocer lugares seguros.",
    "Los gatos pueden reconocer lugares seguros.",
    "Los perros pueden aprender mediante paciencia.",
    "Los gatos pueden aprender mediante paciencia.",
    "Los perros pueden desarrollar hábitos.",
    "Los gatos pueden desarrollar hábitos.",
    "Los perros pueden formar vínculos.",
    "Los gatos pueden formar vínculos.",
    "Los perros pueden reconocer rutinas familiares.",
    "Los gatos pueden reconocer rutinas familiares.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden explorar.",
    "Los gatos pueden explorar.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden recordar.",
    "Los gatos pueden recordar.",
    "Los perros pueden reconocer.",
    "Los gatos pueden reconocer.",
    "Los perros pueden aprender.",
    "Los gatos pueden aprender.",
    "Los perros pueden adaptarse.",
    "Los gatos pueden adaptarse.",
    "Los perros pueden disfrutar.",
    "Los gatos pueden disfrutar.",
    "Los perros pueden crear vínculos.",
    "Los gatos pueden crear vínculos.",
    "Los perros pueden fortalecer relaciones.",
    "Los gatos pueden fortalecer relaciones.",
    "Los perros pueden reconocer señales.",
    "Los gatos pueden reconocer señales.",
    "Los perros pueden aprender asociaciones.",
    "Los gatos pueden aprender asociaciones.",
    "Los perros pueden reconocer patrones.",
    "Los gatos pueden reconocer patrones.",
    "Los perros pueden mostrar preferencias.",
    "Los gatos pueden mostrar preferencias.",
    "Los perros pueden desarrollar hábitos.",
    "Los gatos pueden desarrollar hábitos.",
    "Los perros pueden recordar rutinas.",
    "Los gatos pueden recordar rutinas.",
    "Los perros pueden reconocer rutinas.",
    "Los gatos pueden reconocer rutinas.",
    "Los perros pueden aprender rutinas.",
    "Los gatos pueden aprender rutinas.",
    "Los perros pueden adaptarse a cambios.",
    "Los gatos pueden adaptarse a cambios.",
    "Los perros pueden explorar nuevos espacios.",
    "Los gatos pueden explorar nuevos espacios.",
    "Los perros pueden aprender mediante experiencias.",
    "Los gatos pueden aprender mediante experiencias.",
    "Los perros pueden fortalecer vínculos mediante experiencias positivas.",
    "Los gatos pueden fortalecer vínculos mediante experiencias positivas.",
    "Los perros pueden mostrar confianza.",
    "Los gatos pueden mostrar confianza.",
    "Los perros pueden reconocer espacios seguros.",
    "Los gatos pueden reconocer espacios seguros.",
    "Los perros pueden disfrutar actividades de estimulación.",
    "Los gatos pueden disfrutar actividades de estimulación.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden reconocer personas conocidas.",
    "Los gatos pueden reconocer personas conocidas.",
    "Los perros pueden recordar personas conocidas.",
    "Los gatos pueden recordar personas conocidas.",
    "Los perros pueden reconocer objetos conocidos.",
    "Los gatos pueden reconocer objetos conocidos.",
    "Los perros pueden recordar objetos conocidos.",
    "Los gatos pueden recordar objetos conocidos.",
    "Los perros pueden reconocer sonidos conocidos.",
    "Los gatos pueden reconocer sonidos conocidos.",
    "Los perros pueden recordar sonidos conocidos.",
    "Los gatos pueden recordar sonidos conocidos.",
    "Los perros pueden reconocer olores conocidos.",
    "Los gatos pueden recordar olores conocidos.",
    "Los perros pueden aprender nuevas habilidades.",
    "Los gatos pueden aprender nuevas habilidades.",
    "Los perros pueden disfrutar de juegos.",
    "Los gatos pueden disfrutar de juegos.",
    "Los perros pueden desarrollar preferencias.",
    "Los gatos pueden desarrollar preferencias.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden explorar.",
    "Los gatos pueden explorar.",
    "Los perros pueden aprender.",
    "Los gatos pueden aprender.",
    "Los perros pueden adaptarse.",
    "Los gatos pueden adaptarse.",
    "Los perros pueden recordar.",
    "Los gatos pueden recordar.",
    "Los perros pueden reconocer.",
    "Los gatos pueden reconocer.",
    "Los perros pueden formar vínculos.",
    "Los gatos pueden formar vínculos.",
    "Los perros pueden desarrollar confianza.",
    "Los gatos pueden desarrollar confianza.",
    "Los perros pueden mostrar afecto.",
    "Los gatos pueden mostrar afecto.",
    "Los perros pueden expresar emociones.",
    "Los gatos pueden expresar emociones.",
    "Los perros pueden reconocer señales.",
    "Los gatos pueden reconocer señales.",
    "Los perros pueden aprender señales.",
    "Los gatos pueden aprender señales.",
    "Los perros pueden reconocer patrones.",
    "Los gatos pueden reconocer patrones.",
    "Los perros pueden aprender mediante práctica.",
    "Los gatos pueden aprender mediante práctica.",
    "Los perros pueden aprender mediante interacción.",
    "Los gatos pueden aprender mediante interacción.",
    "Los perros pueden disfrutar de actividades compartidas.",
    "Los gatos pueden disfrutar de actividades compartidas.",
    "Los perros pueden reconocer rutinas.",
    "Los gatos pueden reconocer rutinas.",
    "Los perros pueden aprender rutinas.",
    "Los gatos pueden aprender rutinas.",
    "Los perros pueden adaptarse gradualmente.",
    "Los gatos pueden adaptarse gradualmente.",
    "Los perros pueden recordar experiencias.",
    "Los gatos pueden recordar experiencias.",
    "Los perros pueden reconocer lugares.",
    "Los gatos pueden reconocer lugares.",
    "Los perros pueden explorar espacios.",
    "Los gatos pueden explorar espacios.",
    "Los perros pueden desarrollar hábitos.",
    "Los gatos pueden desarrollar hábitos.",
    "Los perros pueden aprender comportamientos.",
    "Los gatos pueden aprender comportamientos.",
    "Los perros pueden fortalecer vínculos.",
    "Los gatos pueden fortalecer vínculos.",
    "Los perros pueden disfrutar del juego.",
    "Los gatos pueden disfrutar del juego.",
    "Los perros pueden disfrutar del descanso.",
    "Los gatos pueden disfrutar del descanso.",
    "Los perros pueden reconocer ambientes.",
    "Los gatos pueden reconocer ambientes.",
    "Los perros pueden recordar rutinas.",
    "Los gatos pueden recordar rutinas.",
    "Los perros pueden reconocer actividades.",
    "Los gatos pueden reconocer actividades.",
    "Los perros pueden anticipar actividades.",
    "Los gatos pueden anticipar actividades.",
    "Los perros pueden aprender nuevas asociaciones.",
    "Los gatos pueden aprender nuevas asociaciones.",
    "Los perros pueden desarrollar preferencias.",
    "Los gatos pueden desarrollar preferencias.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden reconocer sonidos.",
    "Los gatos pueden reconocer sonidos.",
    "Los perros pueden reconocer olores.",
    "Los gatos pueden reconocer olores.",
    "Los perros pueden reconocer voces.",
    "Los gatos pueden reconocer voces.",
    "Los perros pueden recordar voces.",
    "Los gatos pueden recordar voces.",
    "Los perros pueden aprender nombres.",
    "Los gatos pueden aprender nombres.",
    "Los perros pueden reconocer nombres.",
    "Los gatos pueden reconocer nombres.",
    "Los perros pueden recordar actividades.",
    "Los gatos pueden recordar actividades.",
    "Los perros pueden disfrutar actividades nuevas.",
    "Los gatos pueden disfrutar actividades nuevas.",
    "Los perros pueden adaptarse a nuevas experiencias.",
    "Los gatos pueden adaptarse a nuevas experiencias.",
    "Los perros pueden aprender durante toda su vida.",
    "Los gatos pueden aprender durante toda su vida.",
    "Los perros pueden desarrollar diferentes personalidades.",
    "Los gatos pueden desarrollar diferentes personalidades.",
    "Los perros pueden mostrar diferentes preferencias.",
    "Los gatos pueden mostrar diferentes preferencias.",
    "Los perros pueden reconocer cambios en su entorno.",
    "Los gatos pueden reconocer cambios en su entorno.",
    "Los perros pueden aprender mediante observación.",
    "Los gatos pueden aprender mediante observación.",
    "Los perros pueden aprender mediante experiencia.",
    "Los gatos pueden aprender mediante experiencia.",
    "Los perros pueden crear asociaciones.",
    "Los gatos pueden crear asociaciones.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden disfrutar de experiencias positivas.",
    "Los gatos pueden disfrutar de experiencias positivas.",
    "Los perros pueden formar relaciones sociales.",
    "Los gatos pueden formar relaciones sociales.",
    "Los perros pueden reconocer a otros animales.",
    "Los gatos pueden reconocer a otros animales.",
    "Los perros pueden aprender mediante interacción social.",
    "Los gatos pueden aprender mediante interacción social.",
    "Los perros pueden disfrutar de la compañía.",
    "Los gatos pueden disfrutar de la compañía.",
    "Los perros pueden mostrar preferencias sociales.",
    "Los gatos pueden mostrar preferencias sociales.",
    "Los perros pueden reconocer rutinas familiares.",
    "Los gatos pueden reconocer rutinas familiares.",
    "Los perros pueden recordar rutinas familiares.",
    "Los gatos pueden recordar rutinas familiares.",
    "Los perros pueden aprender nuevas rutinas.",
    "Los gatos pueden aprender nuevas rutinas.",
    "Los perros pueden adaptarse a nuevas rutinas.",
    "Los gatos pueden adaptarse a nuevas rutinas.",
    "Los perros pueden reconocer espacios familiares.",
    "Los gatos pueden reconocer espacios familiares.",
    "Los perros pueden recordar espacios familiares.",
    "Los gatos pueden recordar espacios familiares.",
    "Los perros pueden disfrutar de espacios conocidos.",
    "Los gatos pueden disfrutar de espacios conocidos.",
    "Los perros pueden aprender mediante experiencias positivas.",
    "Los gatos pueden aprender mediante experiencias positivas.",
    "Los perros pueden desarrollar confianza mediante experiencias.",
    "Los gatos pueden desarrollar confianza mediante experiencias.",
    "Los perros pueden fortalecer vínculos mediante experiencias.",
    "Los gatos pueden fortalecer vínculos mediante experiencias.",
    "Los perros pueden mostrar afecto mediante diferentes acciones.",
    "Los gatos pueden mostrar afecto mediante diferentes acciones.",
    "Los perros pueden expresar tranquilidad.",
    "Los gatos pueden expresar tranquilidad.",
    "Los perros pueden expresar entusiasmo.",
    "Los gatos pueden expresar entusiasmo.",
    "Los perros pueden expresar curiosidad.",
    "Los gatos pueden expresar curiosidad.",
    "Los perros pueden expresar interés.",
    "Los gatos pueden expresar interés.",
    "Los perros pueden mostrar confianza.",
    "Los gatos pueden mostrar confianza.",
    "Los perros pueden mostrar seguridad.",
    "Los gatos pueden mostrar seguridad.",
    "Los perros pueden aprender mediante juegos.",
    "Los gatos pueden aprender mediante juegos.",
    "Los perros pueden disfrutar de juegos.",
    "Los gatos pueden disfrutar de juegos.",
    "Los perros pueden desarrollar habilidades mediante juego.",
    "Los gatos pueden desarrollar habilidades mediante juego.",
    "Los perros pueden explorar mediante juego.",
    "Los gatos pueden explorar mediante juego.",
    "Los perros pueden recordar juegos.",
    "Los gatos pueden recordar juegos.",
    "Los perros pueden reconocer juguetes.",
    "Los gatos pueden reconocer juguetes.",
    "Los perros pueden recordar juguetes.",
    "Los gatos pueden recordar juguetes.",
    "Los perros pueden desarrollar preferencias por juguetes.",
    "Los gatos pueden desarrollar preferencias por juguetes.",
    "Los perros pueden desarrollar preferencias por actividades.",
    "Los gatos pueden desarrollar preferencias por actividades.",
    "Los perros pueden aprender diferentes habilidades.",
    "Los gatos pueden aprender diferentes habilidades.",
    "Los perros pueden aprender nuevos comportamientos.",
    "Los gatos pueden aprender nuevos comportamientos.",
    "Los perros pueden aprender nuevas señales.",
    "Los gatos pueden aprender nuevas señales.",
    "Los perros pueden reconocer señales conocidas.",
    "Los gatos pueden reconocer señales conocidas.",
    "Los perros pueden recordar señales conocidas.",
    "Los gatos pueden recordar señales conocidas.",
    "Los perros pueden aprender mediante práctica.",
    "Los gatos pueden aprender mediante práctica.",
    "Los perros pueden aprender mediante repetición.",
    "Los gatos pueden aprender mediante repetición.",
    "Los perros pueden aprender mediante paciencia.",
    "Los gatos pueden aprender mediante paciencia.",
    "Los perros pueden aprender mediante constancia.",
    "Los gatos pueden aprender mediante constancia.",
    "Los perros pueden aprender mediante experiencias.",
    "Los gatos pueden aprender mediante experiencias.",
    "Los perros pueden reconocer patrones.",
    "Los gatos pueden reconocer patrones.",
    "Los perros pueden recordar patrones.",
    "Los gatos pueden recordar patrones.",
    "Los perros pueden anticipar patrones.",
    "Los gatos pueden anticipar patrones.",
    "Los perros pueden reconocer cambios.",
    "Los gatos pueden reconocer cambios.",
    "Los perros pueden recordar cambios.",
    "Los gatos pueden recordar cambios.",
    "Los perros pueden adaptarse a cambios.",
    "Los gatos pueden adaptarse a cambios.",
    "Los perros pueden aprender a adaptarse.",
    "Los gatos pueden aprender a adaptarse.",
    "Los perros pueden explorar.",
    "Los gatos pueden explorar.",
    "Los perros pueden investigar.",
    "Los gatos pueden investigar.",
    "Los perros pueden descubrir.",
    "Los gatos pueden descubrir.",
    "Los perros pueden mostrar curiosidad.",
    "Los gatos pueden mostrar curiosidad.",
    "Los perros pueden recordar experiencias nuevas.",
    "Los gatos pueden recordar experiencias nuevas.",
    "Los perros pueden reconocer experiencias conocidas.",
    "Los gatos pueden reconocer experiencias conocidas.",
    "Los perros pueden aprender de experiencias.",
    "Los gatos pueden aprender de experiencias.",
    "Los perros pueden disfrutar experiencias nuevas.",
    "Los gatos pueden disfrutar experiencias nuevas.",
    "Los perros pueden adaptarse a experiencias nuevas.",
    "Los gatos pueden adaptarse a experiencias nuevas.",
    "Los perros pueden formar vínculos mediante confianza.",
    "Los gatos pueden formar vínculos mediante confianza.",
    "Los perros pueden formar vínculos mediante cariño.",
    "Los gatos pueden formar vínculos mediante cariño.",
    "Los perros pueden formar vínculos mediante cuidado.",
    "Los gatos pueden formar vínculos mediante cuidado.",
    "Los perros pueden reconocer el cuidado.",
    "Los gatos pueden reconocer el cuidado.",
    "Los perros pueden responder al cuidado.",
    "Los gatos pueden responder al cuidado.",
    "Los perros pueden disfrutar del cuidado.",
    "Los gatos pueden disfrutar del cuidado.",
    "Los perros pueden aprender mediante interacción positiva.",
    "Los gatos pueden aprender mediante interacción positiva.",
    "Los perros pueden reconocer interacciones positivas.",
    "Los gatos pueden reconocer interacciones positivas.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden asociar lugares con seguridad.",
    "Los gatos pueden asociar lugares con seguridad.",
    "Los perros pueden asociar personas con seguridad.",
    "Los gatos pueden asociar personas con seguridad.",
    "Los perros pueden asociar rutinas con seguridad.",
    "Los gatos pueden asociar rutinas con seguridad.",
    "Los perros pueden sentirse cómodos con rutinas conocidas.",
    "Los gatos pueden sentirse cómodos con rutinas conocidas.",
    "Los perros pueden disfrutar de ambientes tranquilos.",
    "Los gatos pueden disfrutar de ambientes tranquilos.",
    "Los perros pueden buscar lugares cómodos.",
    "Los gatos pueden buscar lugares cómodos.",
    "Los perros pueden elegir lugares de descanso.",
    "Los gatos pueden elegir lugares de descanso.",
    "Los perros pueden reconocer lugares de descanso.",
    "Los gatos pueden reconocer lugares de descanso.",
    "Los perros pueden recordar lugares de descanso.",
    "Los gatos pueden recordar lugares de descanso.",
    "Los perros pueden desarrollar preferencias por descanso.",
    "Los gatos pueden desarrollar preferencias por descanso.",
    "Los perros pueden desarrollar preferencias por horarios.",
    "Los gatos pueden desarrollar preferencias por horarios.",
    "Los perros pueden reconocer horarios.",
    "Los gatos pueden reconocer horarios.",
    "Los perros pueden recordar horarios.",
    "Los gatos pueden recordar horarios.",
    "Los perros pueden anticipar horarios.",
    "Los gatos pueden anticipar horarios.",
    "Los perros pueden aprender horarios.",
    "Los gatos pueden aprender horarios.",
    "Los perros pueden adaptarse a horarios.",
    "Los gatos pueden adaptarse a horarios.",
    "Los perros pueden desarrollar rutinas.",
    "Los gatos pueden desarrollar rutinas.",
    "Los perros pueden reconocer rutinas.",
    "Los gatos pueden reconocer rutinas.",
    "Los perros pueden recordar rutinas.",
    "Los gatos pueden recordar rutinas.",
    "Los perros pueden anticipar rutinas.",
    "Los gatos pueden anticipar rutinas.",
    "Los perros pueden aprender rutinas.",
    "Los gatos pueden aprender rutinas.",
    "Los perros pueden disfrutar rutinas.",
    "Los gatos pueden disfrutar rutinas.",
    "Los perros pueden sentirse cómodos con rutinas.",
    "Los gatos pueden sentirse cómodos con rutinas.",
    "Los perros pueden aprender mediante hábitos.",
    "Los gatos pueden aprender mediante hábitos.",
    "Los perros pueden desarrollar hábitos.",
    "Los gatos pueden desarrollar hábitos.",
    "Los perros pueden recordar hábitos.",
    "Los gatos pueden recordar hábitos.",
    "Los perros pueden reconocer hábitos.",
    "Los gatos pueden reconocer hábitos.",
    "Los perros pueden adaptar hábitos.",
    "Los gatos pueden adaptar hábitos.",
    "Los perros pueden cambiar hábitos gradualmente.",
    "Los gatos pueden cambiar hábitos gradualmente.",
    "Los perros pueden aprender cambios.",
    "Los gatos pueden aprender cambios.",
    "Los perros pueden reconocer cambios graduales.",
    "Los gatos pueden reconocer cambios graduales.",
    "Los perros pueden adaptarse a cambios graduales.",
    "Los gatos pueden adaptarse a cambios graduales.",
    "Los perros pueden aprender nuevas experiencias.",
    "Los gatos pueden aprender nuevas experiencias.",
    "Los perros pueden recordar nuevas experiencias.",
    "Los gatos pueden recordar nuevas experiencias.",
    "Los perros pueden reconocer nuevas experiencias.",
    "Los gatos pueden reconocer nuevas experiencias.",
    "Los perros pueden disfrutar nuevas experiencias.",
    "Los gatos pueden disfrutar nuevas experiencias.",
    "Los perros pueden explorar nuevas experiencias.",
    "Los gatos pueden explorar nuevas experiencias.",
    "Los perros pueden mostrar curiosidad ante nuevas experiencias.",
    "Los gatos pueden mostrar curiosidad ante nuevas experiencias.",
    "Los perros pueden aprender de nuevas experiencias.",
    "Los gatos pueden aprender de nuevas experiencias.",
    "Los perros pueden adaptarse a nuevas experiencias.",
    "Los gatos pueden adaptarse a nuevas experiencias.",
    "Los perros pueden recordar experiencias agradables.",
    "Los gatos pueden recordar experiencias agradables.",
    "Los perros pueden reconocer experiencias agradables.",
    "Los gatos pueden reconocer experiencias agradables.",
    "Los perros pueden disfrutar experiencias agradables.",
    "Los gatos pueden disfrutar experiencias agradables.",
    "Los perros pueden asociar experiencias con seguridad.",
    "Los gatos pueden asociar experiencias con seguridad.",
    "Los perros pueden asociar experiencias con confianza.",
    "Los gatos pueden asociar experiencias con confianza.",
    "Los perros pueden desarrollar confianza mediante experiencias positivas.",
    "Los gatos pueden desarrollar confianza mediante experiencias positivas.",
    "Los perros pueden fortalecer vínculos mediante experiencias positivas.",
    "Los gatos pueden fortalecer vínculos mediante experiencias positivas.",
    "Los perros pueden reconocer experiencias positivas.",
    "Los gatos pueden reconocer experiencias positivas.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden aprender experiencias positivas.",
    "Los gatos pueden aprender experiencias positivas.",
    "Los perros pueden disfrutar experiencias positivas.",
    "Los gatos pueden disfrutar experiencias positivas.",
    "Los perros pueden responder a experiencias positivas.",
    "Los gatos pueden responder a experiencias positivas.",
    "Los perros pueden mostrar confianza ante experiencias positivas.",
    "Los gatos pueden mostrar confianza ante experiencias positivas.",
    "Los perros pueden mostrar tranquilidad ante experiencias positivas.",
    "Los gatos pueden mostrar tranquilidad ante experiencias positivas.",
    "Los perros pueden desarrollar seguridad mediante experiencias positivas.",
    "Los gatos pueden desarrollar seguridad mediante experiencias positivas.",
    "Los perros pueden formar vínculos mediante experiencias positivas.",
    "Los gatos pueden formar vínculos mediante experiencias positivas.",
    "Los perros pueden aprender mediante experiencias positivas.",
    "Los gatos pueden aprender mediante experiencias positivas.",
    "Los perros pueden reconocer experiencias positivas.",
    "Los gatos pueden reconocer experiencias positivas.",
    "Los perros pueden recordar experiencias positivas.",
    "Los gatos pueden recordar experiencias positivas.",
    "Los perros pueden disfrutar experiencias positivas.",
    "Los gatos pueden disfrutar experiencias positivas.",
    "Los perros pueden fortalecer relaciones mediante experiencias positivas.",
    "Los gatos pueden fortalecer relaciones mediante experiencias positivas.",
    "Los perros pueden desarrollar confianza mediante relaciones positivas.",
    "Los gatos pueden desarrollar confianza mediante relaciones positivas.",
    "Los perros pueden aprender mediante relaciones positivas.",
    "Los gatos pueden aprender mediante relaciones positivas.",
    "Los perros pueden reconocer relaciones positivas.",
    "Los gatos pueden reconocer relaciones positivas.",
    "Los perros pueden recordar relaciones positivas.",
    "Los gatos pueden recordar relaciones positivas.",
    "Los perros pueden disfrutar relaciones positivas.",
    "Los gatos pueden disfrutar relaciones positivas.",
    "Los perros pueden formar relaciones positivas.",
    "Los gatos pueden formar relaciones positivas.",
    "Los perros pueden fortalecer relaciones positivas.",
    "Los gatos pueden fortalecer relaciones positivas.",
    "Los perros pueden crear vínculos positivos.",
    "Los gatos pueden crear vínculos positivos.",
    "Los perros pueden desarrollar vínculos positivos.",
    "Los gatos pueden desarrollar vínculos positivos.",
    "Los perros pueden reconocer vínculos positivos.",
    "Los gatos pueden reconocer vínculos positivos.",
    "Los perros pueden recordar vínculos positivos.",
    "Los gatos pueden recordar vínculos positivos.",
    "Los perros pueden disfrutar vínculos positivos.",
    "Los gatos pueden disfrutar vínculos positivos.",
    "Los perros pueden aprender vínculos positivos.",
    "Los gatos pueden aprender vínculos positivos.",
    "Los perros pueden mostrar vínculos positivos.",
    "Los gatos pueden mostrar vínculos positivos.",
    "Los perros pueden expresar vínculos positivos.",
    "Los gatos pueden expresar vínculos positivos.",
    "Los perros pueden crear confianza.",
    "Los gatos pueden crear confianza.",
    "Los perros pueden desarrollar confianza.",
    "Los gatos pueden desarrollar confianza.",
    "Los perros pueden mantener confianza.",
    "Los gatos pueden mantener confianza.",
    "Los perros pueden fortalecer confianza.",
    "Los gatos pueden fortalecer confianza.",
    "Los perros pueden reconocer confianza.",
    "Los gatos pueden reconocer confianza.",
    "Los perros pueden recordar confianza.",
    "Los gatos pueden recordar confianza.",
    "Los perros pueden aprender confianza.",
    "Los gatos pueden aprender confianza.",
    "Los perros pueden disfrutar confianza.",
    "Los gatos pueden disfrutar confianza.",
    "Los perros pueden mostrar confianza.",
    "Los gatos pueden mostrar confianza.",
    "Los perros pueden expresar confianza.",
    "Los gatos pueden expresar confianza."
];


// ========================================
// SELECCIONAR CONTENIDO SEGÚN LA FECHA
// ========================================

$dia_del_anio = (int) date('z');

$indice_consejo =
    $dia_del_anio % count($consejos_del_dia);

$indice_dato =
    $dia_del_anio % count($datos_curiosos);

$consejo_actual =
    $consejos_del_dia[$indice_consejo];

$dato_actual =
    $datos_curiosos[$indice_dato];

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Inicio - PetMigo
    </title>

    <link
        rel="stylesheet"
        href="estilos.css"
    >

</head>


<body>


<div class="dashboard">


    <!-- =====================================================
         BARRA LATERAL
    ====================================================== -->

    <aside class="sidebar">


        <div class="sidebar-logo">

            <h1>
                PetMigo
            </h1>

        </div>


        <nav>


            <a
                href="inicio.php"
                class="menu-activo"
            >
                Inicio
            </a>


            <a href="mascotas.php">
                Mis mascotas
            </a>


            <a href="citas.php">
                Citas
            </a>


            <a href="calendario.php">
                 Calendario
            </a>


            <a href="alimentacion.php">
                 Alimentación
            </a>


            <a href="recordatorios.php">
                 Recordatorios
            </a>


            <a href="perfil.php">
                 Perfil
            </a>


        </nav>


        <div class="sidebar-footer">

            <a href="cerrar_sesion.php">
                 Cerrar sesión
            </a>

        </div>


    </aside>



    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->

    <main class="main-content">


        <!-- =================================================
             ENCABEZADO
        ================================================== -->

        <header class="top-header">

            <div>

                <h2>

                    ¡Hola,
                    <?php
                    echo htmlspecialchars($nombre);
                    ?>!


                </h2>


                <p>
                    Bienvenido de nuevo a PetMigo
                </p>

            </div>

        </header>



        <!-- =================================================
             BIENVENIDA
        ================================================== -->

        <section class="welcome-card">


            <div>

                <h2>
                    Cuida a tus mascotas con amor
                </h2>


                <p>

                    Lleva el control de la salud,
                    vacunas, citas y cuidados de
                    tus mascotas desde un solo lugar.

                </p>

            </div>


            <div class="welcome-animals">

                🐶 🐱

            </div>


        </section>



        <!-- =================================================
             RESUMEN
        ================================================== -->

        <section class="summary-section">


            <h2>
                Resumen
            </h2>


            <div class="summary-cards">


                <!-- MASCOTAS -->

                <a
                    href="mascotas.php"
                    class="summary-card"
                >

                    <span>
                        🐾
                    </span>


                    <div>

                        <strong>

                            <?php
                            echo $total_mascotas;
                            ?>

                        </strong>


                        <p>

                            <?php

                            echo $total_mascotas == 1
                                ? 'mascota registrada'
                                : 'mascotas registradas';

                            ?>

                        </p>

                    </div>

                </a>



                <!-- VACUNAS -->

                <a
                    href="vacunas.php"
                    class="summary-card"
                >

                    <span>
                        💉
                    </span>


                    <div>

                        <strong>

                            <?php
                            echo $total_vacunas;
                            ?>

                        </strong>


                        <p>

                            <?php

                            echo $total_vacunas == 1
                                ? 'vacuna registrada'
                                : 'vacunas registradas';

                            ?>

                        </p>

                    </div>

                </a>



                <!-- RECORDATORIOS -->

                <a
                    href="recordatorios.php"
                    class="summary-card"
                >

                    <span>
                        🔔
                    </span>


                    <div>

                        <strong>

                            <?php
                            echo $total_recordatorios;
                            ?>

                        </strong>


                        <p>

                            <?php

                            echo $total_recordatorios == 1
                                ? 'recordatorio pendiente'
                                : 'recordatorios pendientes';

                            ?>

                        </p>

                    </div>

                </a>


            </div>


        </section>



        <!-- =================================================
             PRÓXIMAS ACTIVIDADES
        ================================================== -->

        <section class="proximas-actividades">


            <div class="section-title">


                <div>

                    <h2>
                        Próximas actividades
                    </h2>


                    <p>
                        Revisa las próximas citas y recordatorios
                        de tus mascotas.
                    </p>

                </div>


            </div>



            <div class="actividades-grid">


                <!-- PRÓXIMAS CITAS -->

                <div class="actividad-card">


                    <div class="actividad-card-header">

                        <div>

                            <span class="actividad-icon">
                                📅
                            </span>

                            <h3>
                                Próximas citas
                            </h3>

                        </div>


                        <a
                            href="citas.php"
                            class="actividad-link"
                        >
                            Ver citas
                        </a>

                    </div>



                    <div class="actividad-lista">


                        <?php

                        if (
                            $resultado_proximas_citas->num_rows > 0
                        ) {

                            while (
                                $cita =
                                $resultado_proximas_citas->fetch_assoc()
                            ) {

                        ?>

                                <div class="actividad-item">


                                    <div class="actividad-fecha">

                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $cita['fecha']
                                            );

                                            ?>

                                        </strong>


                                        <span>

                                            <?php

                                            echo htmlspecialchars(
                                                $cita['hora']
                                            );

                                            ?>

                                        </span>

                                    </div>


                                    <div class="actividad-info">

                                        <h4>

                                            <?php

                                            echo htmlspecialchars(
                                                $cita['motivo']
                                            );

                                            ?>

                                        </h4>


                                        <p>

                                            🐾

                                            <?php

                                            echo htmlspecialchars(
                                                $cita['mascota']
                                            );

                                            ?>

                                        </p>


                                        <?php

                                        if (
                                            !empty($cita['lugar'])
                                        ) {

                                        ?>

                                            <span>

                                                📍

                                                <?php

                                                echo htmlspecialchars(
                                                    $cita['lugar']
                                                );

                                                ?>

                                            </span>

                                        <?php

                                        }

                                        ?>

                                    </div>


                                    <span class="actividad-estado">

                                        <?php

                                        echo htmlspecialchars(
                                            $cita['estado']
                                        );

                                        ?>

                                    </span>


                                </div>

                        <?php

                            }

                        } else {

                        ?>

                            <div class="actividad-vacia">

                                <span>
                                    📅
                                </span>

                                <p>
                                    No tienes citas próximas.
                                </p>

                            </div>

                        <?php

                        }

                        ?>


                    </div>


                </div>



                <!-- PRÓXIMOS RECORDATORIOS -->

                <div class="actividad-card">


                    <div class="actividad-card-header">

                        <div>

                            <span class="actividad-icon">
                                🔔
                            </span>

                            <h3>
                                Próximos recordatorios
                            </h3>

                        </div>


                        <a
                            href="recordatorios.php"
                            class="actividad-link"
                        >
                            Ver recordatorios
                        </a>

                    </div>



                    <div class="actividad-lista">


                        <?php

                        if (
                            $resultado_proximos_recordatorios->num_rows > 0
                        ) {

                            while (
                                $recordatorio =
                                $resultado_proximos_recordatorios->fetch_assoc()
                            ) {

                        ?>

                                <div class="actividad-item">


                                    <div class="actividad-fecha">

                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $recordatorio['fecha']
                                            );

                                            ?>

                                        </strong>


                                        <span>

                                            <?php

                                            echo htmlspecialchars(
                                                $recordatorio['hora']
                                            );

                                            ?>

                                        </span>

                                    </div>


                                    <div class="actividad-info">

                                        <h4>

                                            <?php

                                            echo htmlspecialchars(
                                                $recordatorio['titulo']
                                            );

                                            ?>

                                        </h4>


                                        <p>

                                            🐾

                                            <?php

                                            echo htmlspecialchars(
                                                $recordatorio['mascota']
                                            );

                                            ?>

                                        </p>


                                        <?php

                                        if (
                                            !empty(
                                                $recordatorio['descripcion']
                                            )
                                        ) {

                                        ?>

                                            <span>

                                                <?php

                                                echo htmlspecialchars(
                                                    $recordatorio['descripcion']
                                                );

                                                ?>

                                            </span>

                                        <?php

                                        }

                                        ?>

                                    </div>


                                    <span
                                        class="actividad-estado pendiente"
                                    >
                                        Pendiente
                                    </span>


                                </div>

                        <?php

                            }

                        } else {

                        ?>

                            <div class="actividad-vacia">

                                <span>
                                    🔔
                                </span>

                                <p>
                                    No tienes recordatorios próximos.
                                </p>

                            </div>

                        <?php

                        }

                        ?>


                    </div>


                </div>


            </div>


        </section>



        <!-- =================================================
             CONSEJO DEL DÍA Y DATO CURIOSO
        ================================================== -->

        <section class="contenido-diario">


            <!-- CONSEJO DEL DÍA -->

            <div class="contenido-diario-card consejo-card">

                <div class="contenido-diario-icono">
                    🐾
                </div>


                <div class="contenido-diario-texto">

                    <h2>
                        Consejo del día
                    </h2>


                    <p>

                        <?php

                        echo htmlspecialchars(
                            $consejo_actual
                        );

                        ?>

                    </p>

                </div>

            </div>



            <!-- DATO CURIOSO -->

            <div class="contenido-diario-card dato-card">

                <div class="contenido-diario-icono">
                    💡
                </div>


                <div class="contenido-diario-texto">

                    <h2>
                        Dato curioso
                    </h2>


                    <p>

                        <?php

                        echo htmlspecialchars(
                            $dato_actual
                        );

                        ?>

                    </p>

                </div>

            </div>


        </section>


    </main>


</div>


</body>

</html>


<?php

$stmt_proximas_citas->close();

$stmt_proximos_recordatorios->close();

$conexion->close();

?>