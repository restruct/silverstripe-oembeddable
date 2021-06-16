import $ from 'jquery';

(function($) {

    $('body').on('click', '.loadEmbeddableData', function(event){

      let params = {
        SecurityID: $('input[name=SecurityID]').val(),
        OEmbedURL: $(this).parents('.embeddableUrl').find('input[name$="[sourceurl]"]').val()
      };

      let button = $(this);
      button.val(button.data('trnslLoading'));
//      button.prop('disabled', 'disabled');

      $.post(
        button.data('href'),
        params,
        function(xhr_result){
            button.val(button.data('trnslInspect'));
            button.removeAttr('disabled');
            if (xhr_result && xhr_result.length) {
              let replacement = $(xhr_result);
              replacement.find('input[name$="[sourceurl]"]').val(params.OEmbedURL);
              button.parents('.oembeddable').replaceWith( replacement )

            }
        });
    });

})(jQuery);
