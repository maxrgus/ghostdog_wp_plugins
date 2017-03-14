jQuery(document).ready(function($){

    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;
    var id;
    var sd;
    var numb;

    // Runs when the image button is clicked.
    $('.set_image').click(function(e){
        // get anchor id
        id = $(this).attr('id');
        // Prevents the default action from occuring.
        e.preventDefault();

        // If the frame already exists, re-open it.
        if ( meta_image_frame ) {
            meta_image_frame.open();
            return;
        }

        // Sets up the media library frame
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }
        });

        // Runs when an image is selected.
        meta_image_frame.on('select', function(){

            //Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
            var thumb = media_attachment.sizes.post_thumbnail_large.url;
            alert(thumb);
            // Sends the attachment URL to our custom image input field.
            document.getElementById(id + '_url').src=(media_attachment.url);
            $('#url_to_' + id).val(thumb);

        });
        // Opens the media library frame.
        meta_image_frame.open();
        $("a").siblings(".remove_image").css("display","block");

    });
    $('.remove_image').click(function(e){
      e.preventDefault();
      id = $(this).attr('id');
      id = parseInt(id);
      document.getElementById('image' + id + '_url').src="";
      $('#url_to_image' + id).val('');
      $(this).css("display", "none");
    });
});
