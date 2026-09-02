<div class="tweetdis_settings_wrap tweetdis_clearfix">
    

    <div class="tweetdis_left">
        
            <h3>Preview:</h3>
            <div class="box_preview tweet">
                
                <div class="box_example">
                    
                    <div class="tweetdis_clearfix">
                        <div class="tweetdis_left">
                            <img src="<?= esc_url( Tweetdis_Settings::get_instance()->get_images_url() ) ?>timface.jpeg" alt="author"/>
                            <p><strong>Tim Soulo</strong></p>
                            <span>@timsoulo</span>
                        </div>

                        <div class="tweetdis_right">
                            <img src="<?= esc_url( Tweetdis_Settings::get_instance()->get_images_url() ) ?>tweet_btns.png" alt="btns"/>
                        </div>
                    </div>
                    
                    <p id="box_example_text">
                        <i class="preposition_before"></i><span>TweetDis is an awesome plugin for Wordpress, that makes any phrase "tweetable".</span>
                        <a href="#" target="_blank"></a><i class="preposition_after"></i>
                    </p>
                    
                    <img src="<?= esc_url( Tweetdis_Settings::get_instance()->get_images_url() ) ?>tweet_links.png" alt="links"/>
                    
                </div>
                
            </div>
        
    </div>
    
    
    <div class="tweetdis_left form_settings">
        
            <h3>Settings:</h3>

            <div class="tweetdis_form_row tweetdis_clearfix">
                <label for="preposition">Preposition:</label>
                
                <div class="input_wrap">
                    <input type="radio" name="preposition" value="RT">
                    <label>RT</label>
                    <input type="radio" name="preposition" value="by">
                    <label>by</label>
                    <input type="radio" name="preposition" value="via">
                    <label>via</label>                 
                     <input type="radio" name="preposition" value="none">
                    <label>none</label>                   
                </div>
            </div>

            <div class="tweetdis_form_row tweetdis_clearfix">
                <label for="twitter">Default X / Twitter account:</label>
                <input type="text" id="twitter" value="<?= esc_attr( $settings['twitter'] ) ?>"/>
            </div>
            
            <div class="tweetdis_form_row tweetdis_clearfix">
                <label for="follow">Recommend to follow:</label>
                <input type="text" id="follow" value="<?= esc_attr( $settings['follow'] ) ?>"/>
            </div>
        
            <div class="tweetdis_form_row tweetdis_clearfix reduced_margin tweetdis_text_right">
                <button id="save_settings" class="tweetdis_button">Save All Changes</button>
                <p class="input_comment saved"></p>
            </div>
        
    </div>

</div>
<!-- tweetdis_settings_wrap -->

<script type="text/javascript" data-cfasync="false">
    var $j = jQuery.noConflict();
    
    
    /* Get preview */
    
    function toggle_inputs() {
        $j('input[value="' + request.preposition +'"]').attr('checked', 'true');
    }
    
    function show_preposition() {
        
        $tweet = $j('#box_example_text');
        $tweet.find('i').html('');
        
        if (request.twitter.length > 0) {
            
            switch (request.preposition) {

                case 'RT': $tweet.find('.preposition_before').html(request.preposition + ' <span>@'+ request.twitter +'</span> ');
                    break;
                case 'none': $tweet.find('.preposition_after').html(' <span>@' + request.twitter + '</span>');
                    break;
                default:   $tweet.find('.preposition_after').html(' ' + request.preposition +' <span>@' + request.twitter + '</span>');
                    break;
            }
            
        }
    }
    
    function show_link() {
        var link = window.location.origin || '';
        $j('#box_example_text a').html(link).attr('href', link);
    }
    
    
    var request = {
        action: 'tweetdis_save_settings',
        tabs: 'tweet',
        twitter: <?= wp_json_encode( $settings['twitter'] ) ?>,
        follow: <?= wp_json_encode( $settings['follow'] ) ?>,
        preposition: <?= wp_json_encode( $settings['preposition'] ) ?>
    };
    
    toggle_inputs();
    show_preposition();
    show_link();
    
    $j('input[name="preposition"]').on('change', function() {
        request.preposition = $j('input[name="preposition"]:checked').val();
        show_preposition();
    });
    
    $j('#twitter').on('input', function() {
        request.twitter = $j(this).val();
        show_preposition();
    });
    
    $j('#save_settings').on('click', function() {

        request.follow = $j('#follow').val(); 

        $j.ajax({
            type: 'POST',
            url: Td_Ajax.ajaxurl,
            data: request,
            success: function (msg) {
                $placeholder = $j('#save_settings').next('.input_comment');
                $placeholder.html(msg);
                setTimeout (function() {
                    $placeholder.html('');
                }, 3000);
            }
        });
    });
</script>
