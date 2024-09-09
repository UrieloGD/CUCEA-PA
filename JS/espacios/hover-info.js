$(document).on('mouseenter', '.sala[data-info]', function(e) {
    var info = JSON.parse($(this).attr('data-info'));
    var infoHtml = '<div class="info-hover">' +
                '<p><strong>CVE Materia:</strong> ' + info.cve_materia + '</p>' +
                '<p><strong>Materia:</strong> ' + info.materia + '</p>' +
                '<p><strong>Profesor:</strong> ' + info.profesor + '</p>' +
                '</div>';
    var $infoElement = $(infoHtml).appendTo('body');
    
    var salaRect = this.getBoundingClientRect();
    var infoRect = $infoElement[0].getBoundingClientRect();
    
    var top = salaRect.top - infoRect.height - 10;
    var left = salaRect.left + (salaRect.width / 2) - (infoRect.width / 2);
    
    $infoElement.css({
        position: 'fixed',
        top: Math.max(0, top) + 'px',
        left: Math.max(0, left) + 'px'
    });
    
    $(this).data('infoElement', $infoElement);
}).on('mouseleave', '.sala[data-info]', function() {
    var $infoElement = $(this).data('infoElement');
    if ($infoElement) {
        $infoElement.remove();
    }
});