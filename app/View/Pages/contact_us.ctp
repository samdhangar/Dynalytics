<div class="col-md-12 slider-last">
	<div class="title-slider-main-in-page">Contact Us</div>
	<div class="col-md-12 contact-main">
		<div class="row">
			<section class="slice no-padding">
				<div id="mapCanvas" class="map-canvas no-margin"></div>
			</section>
			<div class="col-md-7 inner-contact">
                <?php echo $this->Form->create('ContactUs',array('inputDefaults'=>array('label'=>false,'div'=>false)))?>
				<div class="row">
					<h1>Contact Form</h1>
					<ul>
						<li>
                            <?php echo $this->Form->input('name',array('before'=>'<span>Name: </span>','placeholder'=>__('Your Name')))?>
						</li>
						<li>
							<?php echo $this->Form->input('email',array('before'=>'<span>Email: </span>','placeholder'=>__('Your Email Address')))?>
						</li>
						<li>
							<?php echo $this->Form->input('subject',array('before'=>'<span>Subject: </span>','placeholder'=>__('Query title')))?>
						</li>
						<li>
							<?php echo $this->Form->input('message',array('type'=>'textarea','before'=>'<span>Message: </span>','placeholder'=>__('Your Query')))?>
						</li>
                        <li>
                            <span></span>
                            <?php echo $this->Form->submit('Contact Now',array('class'=>'btn btn-primary'));?>
                        </li>
					</ul>
				</div>
                <?php echo $this->Form->end();?>
			</div>
			<div class="col-md-5">
				<div class="row">
					<h1>Contact Information</h1>
					<div class="contact-info">
						<div class="address-info">
							<h2>Address</h2>
							<p>
                                G31,Anumpam Ameniety Shopping Centre<br />
                                Udhna bus depo, Udhna Main Road, Surat<br />
                                Gujarat - India
                            </p>
						</div>
						<div class="address-info">
							<h2>Email</h2>
							<p>
                                <a href="mailto:info@rgplacement.com">info@rgplacement.com</a><br>
                                <a href="mailto:rgplacementservice@gmail.com">rgplacementservice@gmail.com</a>
                            </p>
						</div>
						<div class="address-info">
							<h2>Phone</h2>
							<p>Mobile : +91 0261-2277800</p>
						</div>
						<div class="address-info">
							<h2>Website</h2>
                            <p><?php echo $this->Html->link(Configure::read('Site.Url'),Configure::read('Site.Url'))?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>
<script type="text/javascript">
	function initialize() {
		var myLatlng = new google.maps.LatLng(21.1663661,72.8414868);
		var mapOptions = {
			zoom: 17,
			scrollwheel: false,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		var map = new google.maps.Map(document.getElementById('mapCanvas'), mapOptions);		
		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			animation: google.maps.Animation.DROP,
		});
		  
		var contentString = '<div class="info-window-content"><h2>Rg Placement Services</h2>'+
							  '<p>G31,Anumpam Ameniety Shopping Centre<br>Udhna bus depo, Udhna Main Road, Surat<br>Gujarat - India<br>Phone: 0261-227-7800</p></div>';							  
		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});
	}
		
	google.maps.event.addDomListener(window, 'load', initialize);    
</script>
