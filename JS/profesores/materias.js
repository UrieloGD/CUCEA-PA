$(document).ready(function() {
    // Ocultar todas las secciones excepto la activa al inicio
    $('.curso-seccion').hide();
    $('#todas').show();
    
    // Manejar clics en los items de navegación
    $('.nav-item').click(function(e) {
        e.preventDefault();
        
        // Remover clase activa de todos los items y agregarla al clickeado
        $('.nav-item').removeClass('active');
        $(this).addClass('active');
        
        // Ocultar todas las secciones y mostrar la seleccionada
        const targetId = $(this).data('section');
        $('.curso-seccion').hide();
        $(`#${targetId}`).show().css('opacity', 0).animate({opacity: 1}, 200);

        // Desplazamiento mejorado para centrar el elemento activo
        const $navContainer = $('.nav-items-container');
        const $clickedNavItem = $(this);
        
        // Calcular dimensiones precisas
        const containerWidth = $navContainer.width();
        const scrollContainer = $navContainer[0];
        const itemOffset = $clickedNavItem.position().left;
        const itemWidth = $clickedNavItem.outerWidth();
        
        // Calcular posición de desplazamiento centrada
        const scrollPosition = 
            itemOffset - (containerWidth / 2) + (itemWidth / 2) + $navContainer.scrollLeft();
        
        // Desplazamiento suave centrado
        $navContainer.animate({
            scrollLeft: scrollPosition
        }, {
            duration: 300,
            easing: 'swing',
            complete: function() {
                // Asegurar que el elemento permanezca completamente visible
                const navItemLeft = $clickedNavItem.position().left;
                const navItemRight = navItemLeft + $clickedNavItem.outerWidth();
                const containerLeft = 0;
                const containerRight = $navContainer.width();

                if (navItemLeft < containerLeft || navItemRight > containerRight) {
                    scrollContainer.scrollLeft = scrollPosition;
                }
            }
        });
        
        // Actualizar estado de las flechas
        updateArrows();
    });
    
    // Función para actualizar el estado de las flechas
    function updateArrows() {
        const activeIndex = $('.nav-item.active').index();
        const totalItems = $('.nav-item').length;
        
        $('.prev-arrow').prop('disabled', activeIndex === 0);
        $('.next-arrow').prop('disabled', activeIndex === totalItems - 1);
    }
    
    // Manejar clics en las flechas
    $('.prev-arrow').click(function() {
        if (!$(this).prop('disabled')) {
            const activeItem = $('.nav-item.active');
            activeItem.prev('.nav-item').click();
        }
    });
    
    $('.next-arrow').click(function() {
        if (!$(this).prop('disabled')) {
            const activeItem = $('.nav-item.active');
            activeItem.next('.nav-item').click();
        }
    });
    
    // Inicializar estado de las flechas
    updateArrows();
});

$(document).ready(function() {
    // Funcionalidad de búsqueda
    $('#search-input').on('keyup', function() {
        const searchText = $(this).val().toLowerCase().trim();
        
        // Si la búsqueda está vacía, muestra todos los cursos en la sección activa
        if (searchText === '') {
            $('.curso-seccion.active tbody tr').show();
            return;
        }

        // Obtiene la sección activa
        const $activeSection = $('.curso-seccion.active');
        
        // Filtra filas en la sección activa
        $activeSection.find('tbody tr').each(function() {
            const $row = $(this);
            const rowText = $row.text().toLowerCase();
            
            // Ocultar/mostrar fila en función de la coincidencia de búsqueda
            if (rowText.includes(searchText)) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    });

    $('.nav-item').click(function() {
        $('#search-input').val('').trigger('keyup');
    });
});

