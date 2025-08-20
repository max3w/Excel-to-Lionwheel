<?php

if (isset($_GET['excel_order_success'])) {
	echo '<div class="excel-order-success" style="background:#d4edda;color:#155724;padding:15px;margin:20px 0;border:1px solid #c3e6cb;border-radius:4px;">
        ✅ Your order has been successfully sent! We will contact you shortly.
    </div>';
}

if (isset($_GET['excel_order_error'])) {
	$error_type = sanitize_text_field($_GET['excel_order_error']);
	$errors = [
		'invalid_file'   => 'Invalid file type. Please upload only .xlsx or .xls',
		'file_too_large' => 'File size exceeds 10MB.',
		'upload_error'   => 'File upload error.',
		'database'       => 'Database error. Please try again later.',
		'security'       => 'Security error. Please refresh the page and try again.',
		'required_fields' => 'Please fill all required fields.',
	];

	if (isset($errors[$error_type])) {
		echo '<div class="excel-order-error" style="background:#f8d7da;color:#721c24;padding:15px;margin:20px 0;border:1px solid #f5c6cb;border-radius:4px;">
            ❌ Error: ' . esc_html($errors[$error_type]) . '
        </div>';
	}
}
?>

<main>
	<section class="gifts-hero" style="background-image: url('img/hero-bg.jpg');">
		<div class="container">
			<div class="gifts-hero__wrap">
				<h1>שוגליז B2B - מתנות מושלמות לכל העובדים בקליק</h1>
			</div>
		</div>
	</section>

	<section class="gifts-video">
		<div class="container">
			<div class="ss-video">
				<div class="ss-video__cover">
					<div class="ss-video__btn-play button-popup" data-popup="popup-video"></div>
					<h2>שוגליז B2B - תהליך הזמנה מהיר לעסקים</h2>
				</div>
				<!-- <div class="ss-video__player"></div> -->
			</div>
		</div>
	</section>

	<section class="gifts-steps">
		<div class="container">
			<div class="gifts-steps__wrap">
				<h2>העלאת קובץ אקסל עם רשימת העובדים - הדרך המהירה להזמין מתנות לעובדים בקליק</h2>
				<div class="gifts-steps__list">
					<div class="gifts-step step-1 active">
						<div class="gifts-step__head">
							<ul class="gifts-step__progress-bar">
								<li class="active">
									<span>1</span>
									<p>הורדת הקובץ</p>
								</li>
								<li>
									<span>2</span>
									<p>העלאת הקובץ</p>
								</li>
								<li>
									<span>3</span>
									<p>השלמת פרטים</p>
								</li>
							</ul>
							<div class="gifts-step__descr">
								<h3>הורדת קובץ האקסל תאפשר לכם למלא את פרטי העובדים ולבצעה הזמנה בקליק</h3>
								<p><strong>הורידו את קובץ האקסל,</strong>מלאו את כל השדות <br> עם פרטי העובדים והעלו את הקובץ בשלב
									2.</p>
							</div>
						</div>
						<div class="gifts-step__body">
							<div class="gifts-step__icon bg-white">
								<img src="/wp-content/plugins/excel-to-lionwheel/assets/img/file.svg" alt="icon xls">
							</div>
							<h5>שוגליז B2B - הורדת קובץ אקסל</h5>
							<button class="btn-main btn-main_icon btn-main_download button-popup"
								data-popup="popup-contacts">הורדה</>
						</div>
					</div>
					<div class="gifts-step step-2">
						<div class="gifts-step__head">
							<ul class="gifts-step__progress-bar">
								<li class="done">
									<span>1</span>
									<p>הורדת הקובץ</p>
								</li>
								<li class="active">
									<span>2</span>
									<p>העלאת הקובץ</p>
								</li>
								<li>
									<span>3</span>
									<p>השלמת פרטים</p>
								</li>
							</ul>
							<div class="gifts-step__descr">
								<h3>כאן ניתן להעלות את קובץ האקסל -על מנת להשלים את תהליך ההזמנה</h3>
								<p><strong>ש למלא
										ולהעלות את הקובץ עם השדות הבאים:</strong>שם פרטי, שם משפחה, טלפון, עיר, רחוב, מספר בניין (חשוב
									לשמור על מבנה העמודות בקובץ).</p>
							</div>
						</div>
						<div class="gifts-step__body">

							<form id="form-upload-file">
								<label>
									<input type="file" name="file" id="file" accept=".xlsx, .xls, .csv"
										class="gifts-step__file-input">
									<div class="gifts-step__icon bg-gray">
										<img src="/wp-content/plugins/excel-to-lionwheel/assets/img/file.svg" alt="icon xls">
									</div>
									<div class="btn-main btn-main_icon btn-main_upload">
										<span>העלאת קובץ</span>

									</div>
									<h6>גררו את הקובץ לכאן או ביחרו קובץ שמור</h6>
									<h5>שוגליז B2B - העלאת קובץ אקסל</h5>

								</label>
							</form>
						</div>
					</div>
					<div class="gifts-step step-3">
						<div class="gifts-step__head">
							<ul class="gifts-step__progress-bar">
								<li class="done">
									<span>1</span>
									<p>הורדת הקובץ</p>
								</li>
								<li class="done">
									<span>2</span>
									<p>העלאת הקובץ</p>
								</li>
								<li class="active">
									<span>3</span>
									<p>השלמת פרטים</p>
								</li>
							</ul>
							<div class="gifts-step__descr">
								<h3>על מנת להשלים ולשלוח את הקובץ יש למלא את הפרטים </h3>
								<p><strong>הפרטים שתמלאו כאן יהיו חלק מתהליך
										ההזמנה. </strong><br>לאחר העלאת הקובץ נעבור על הפרטים וניצור איתכם קשר.</p>
							</div>
						</div>
						<div class="gifts-step__body">
							<h2>השלמת תהליך הזמנה B2B</h2>
							<h5>אנא מלאו את הפרטים הבאים: </h5>
							<form id="form-order" method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
								<?php wp_nonce_field('excel_order_nonce', '_wpnonce'); ?>
								<input type="hidden" name="action" value="excel_order_submit">

								<label>
									<input type="text" name="name" placeholder="* שם החברה" required>
								</label>

								<label>
									<input type="text" name="company_invoice" placeholder="* שם החברה לחשבונית" required>
								</label>

								<label>
									<input type="text" name="registration_id" placeholder="* ח.פ / ע.מ / ת.ז" required>
								</label>

								<label>
									<input type="email" name="email" placeholder="* מייל" required>
								</label>

								<label>
									<input type="text" name="contact_name" placeholder="* שם איש קשר" required>
								</label>

								<label>
									<input type="tel" name="phone" placeholder="* נייד איש קשר" required>
								</label>

								<label>
									<input type="text" name="extra_contact" placeholder="שם איש קשר נוסף">
								</label>
								<span class="messages"></span>
								<button type="submit" class="btn-main">שליחה</button>
							</form>
						</div>
					</div>
				</div>
			</div>
	</section>
</main>

<div id="popup-video" class="popup">
	<div class="popup__body">
		<div class="popup__overlay"></div>
		<div class="popup__content">
			<button class="popup__btn-close"><img src="/wp-content/plugins/excel-to-lionwheel/assets/img/btn-close.svg" alt="close"></button>
			<video class="popup__video" src="/wp-content/plugins/excel-to-lionwheel/assets/files/intro.mp4" controls></video>
		</div>
	</div>
</div>
<div id="popup-contacts" class="popup">
	<div class="popup__body">
		<div class="popup__overlay"></div>
		<div class="popup__content">
			<button class="popup__btn-close"><img src="/wp-content/plugins/excel-to-lionwheel/assets/img/btn-close.svg" alt="close"></button>
			<!-- content -->
			<h2>פרטי המזמין</h2>
			<p>אנא מלאו את פרטי ההתקשרות עבור המשך ההזמנה</p>
			<form id="form-contacts">
				<label>
					<input type="email" name="email" placeholder="* מייל" required>
				</label>
				<label>
					<input type="tel" name="phone" placeholder="* נייד איש קשר" required>
				</label>
				<span class="messages"></span>
				<button type="submit" class="btn-main">שליחה</button>
			</form>
		</div>
	</div>
</div>



<!-- 
<div class="excel-order-form">
	<h2>Order from Excel</h2>
	<form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
		<?php wp_nonce_field('excel_order_nonce', '_wpnonce'); ?>
		<input type="hidden" name="action" value="excel_order_submit">

		<div class="form-group">
			<label for="name">Name:</label>
			<input type="text" name="name" id="name" required>
		</div>

		<div class="form-group">
			<label for="phone">Phone:</label>
			<input type="tel" name="phone" id="phone" required>
		</div>

		<div class="form-group">
			<label for="email">Email:</label>
			<input type="email" name="email" id="email" required>
		</div>

		<div class="form-group">
			<label for="excel_file">Excel order file:</label>
			<input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" required>
			<small>.xlsx and .xls files are supported (max. 10MB)</small>
		</div>

		<div class="form-group">
			<input type="submit" value="Send order">
		</div>
	</form>
</div> -->