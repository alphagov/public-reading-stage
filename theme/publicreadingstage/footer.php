<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>

				<div class="row" id='footer'>

					<div class="twelvecol last">

						<ul class="xoxo">
							<?php dynamic_sidebar( 'footer-widget-area' ); ?>
						</ul>
											
					</div>

				</div>
				

			</div><!-- container -->

<!--  other analytics code -->

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-89220-41']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<!--  end other analytics code -->

<script type='text/javascript'>
	jQuery(document).ready(function(){
		jQuery('#toccontent').hide();
		
		jQuery('#tocinner').mouseover(function(){
			jQuery('#toccontent').show();
		});
		
		jQuery('#tocinner').mouseout(function(){
			jQuery('#toccontent').hide();
		});
	});

	openNotesInLightbox();
    markDocumentLinks();
    gaTrackDownloadableFiles();
</script>
		
<!-- design & implementation by Helpful Technology http://www.helpfultechnology.com for the Government Digital Service, using WordPress -->

<?php
	wp_footer();
?>
</body>
</html>