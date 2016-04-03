/* =Admin JS
----------------------------------------------- */
(function( $ ) {

    /** Options **/
    var form = $( '.bulk-update-form' ),
        message = form.find( '.message' ),
        spinner = form.find( '.spin-it' ),
        button = form.find( '.button' );

    /* Update posts */
    form.on( 'submit', function(e) {
    	e.preventDefault();

        var posts = [], i = 0;

        // Fetch all post data
        form.find( '.bulk-update-post' ).each( function( index, val ) {
            var post = {};
            $(this).find( 'input' ).each( function( i, val ) {
                post[val.name] = val.value;
            });
            post.id = $(this).data( 'id' );
            posts[i] = post;
            i++;
        });

        // Set data
        var data = {
            action: 'bulk_updater_update',
            posts: posts
        };

        button.attr( 'disabled', 'disabled' );
        spinner.addClass( 'is-visible' );
        message.removeClass( 'is-visible success error' );

        // Process action
        $.post( ajaxurl, data )
        	.done( function( data ) {
            	spinner.removeClass( 'is-visible' );
        	} )
			.fail( function( data ) {
				message.addClass( 'error' );
        	} )
        	.always( function( data ) {
        		data = $.parseJSON( data );
        		message.addClass( 'is-visible ' + data.status ).html( data.message );
                button.removeAttr( 'disabled' );
        	} );

    });

})( jQuery );
