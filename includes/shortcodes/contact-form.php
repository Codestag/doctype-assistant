<?php
/**
 * Contact Form Shortcode
 *
 * Displays a contact form.
 *
 * @package Doctype Assistant
 * @subpackage Doctype
 */
function doctype_contact_form_sc() {
$nameError         = __( 'Please enter your name.', 'doctype-assistant' );
$emailError        = __( 'Please enter your email address.', 'doctype-assistant' );
$emailInvalidError = __( 'You entered an invalid email address.', 'doctype-assistant' );
$commentError      = __( 'Please enter a message.', 'doctype-assistant' );

$errorMessages     = array();

if ( isset( $_POST['submitted'] ) ) {

    if ( trim( $_POST['contactName'] ) === '' ) {
        $errorMessages['nameError'] = $nameError;
        $hasError = true;
    } else {
        $name = trim( $_POST['contactName'] );
    }

    if ( trim( $_POST['email'] ) === '' ) {
        $errorMessages['emailError'] = $emailError;
        $hasError = true;
    } else if ( ! is_email( trim( $_POST['email'] ) ) ) {
        $errorMessages['emailInvalidError'] = $emailInvalidError;
        $hasError = true;
    } else {
        $email = trim($_POST['email']);
    }

    if(trim($_POST['comments']) === '') {
        $errorMessages['commentError'] = $commentError;
        $hasError = true;
    } else {
        if ( function_exists('stripslashes') ) {
            $comments = stripslashes(trim($_POST['comments']));
        } else {
            $comments = trim($_POST['comments']);
        }
    }

    if ( !isset($hasError) ) {
        $emailTo = doctype_get_thememod_value('doctype_contact_email');
        if (!isset($emailTo) || ($emailTo == '') ){
            $emailTo = get_option('admin_email');
        }
        $subject = '[Contact Form] From '.$name;

        $body = "Name: $name \n\nEmail: $email \n\nMessage: $comments \n\n";
        $body .= "--\n";
        $body .= "This mail is sent via contact form on ".get_bloginfo('name')."\n";
        $body .= home_url();

        $headers = 'From: '.$name.' <'.$email.'>' . "\r\n" . 'Reply-To: ' . $email;

        wp_mail($emailTo, $subject, $body, $headers);
        $emailSent = true;
    }
}
?>

<div class="contact-form-wrapper">
	<?php if( isset( $emailSent ) && true === $emailSent ) : ?>

		<div class="stag-alert stag-alert--green">
			<p><?php _e('Thanks, your email was sent successfully.', 'doctype-assistant') ?></p>
		</div>

	<?php else: ?>

		<form action="<?php the_permalink(); ?>" id="contactForm" class="contact-form" method="post">
			<h2><?php _e('Send a Direct Message', 'doctype-assistant'); ?></h2>

			<div class="grids">
				<p class="grid-6">
					<label for="contactName"><?php _e('Name', 'doctype-assistant') ?></label>
					<input type="text" name="contactName" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>">
					<?php if(isset($errorMessages['nameError'])) { ?>
						<span class="error"><?php echo $errorMessages['nameError']; ?></span>
					<?php } ?>
				</p>

				<p class="grid-6">
					<label for="email"><?php _e('Email', 'doctype-assistant') ?></label>
					<input type="text" name="email" id="email" value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>">
					<?php if(isset($errorMessages['emailError'])) { ?>
						<span class="error"><?php echo $errorMessages['emailError']; ?></span>
					<?php } ?>
					<?php if(isset($errorMessages['emailInvalidError'])) { ?>
						<span class="error"><?php echo $errorMessages['emailInvalidError']; ?></span>
					<?php } ?>
				</p>
			</div>

			<p class="commentsText">
				<label for="commentsText"><?php _e('Comment', 'doctype-assistant') ?></label>
				<textarea rows="8" name="comments" id="commentsText" ><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
				<?php if(isset($errorMessages['commentError'])) { ?>
					<span class="error"><?php echo $errorMessages['commentError']; ?></span>
				<?php } ?>
			</p>

			<p class="buttons">
				<input type="submit" id="submitted" class="contact-form-button" name="submitted" value="<?php _e('Send Message', 'doctype-assistant') ?>">
			</p>
		</form>

	<?php endif; ?>
</div>
<?php
}
add_shortcode( 'doctype_contact_form', 'doctype_contact_form_sc' );
