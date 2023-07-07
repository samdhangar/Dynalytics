<div class="row">
	<div class="col-md-6 col-sm-6 con-clients">
		<?php echo $this->Html->image('client-icon.png')?>
		<h1>CLIENTS</h1>
		<p>
            RG Placement aims to set the clients’ interest above everything else. We are committed to the cause of offering you the perfect candidate from among our extensive talent pool, at the fastest turnaround time. 
		</p>
		<div class="btn-black left-space">
            <?php echo $this->Html->link(__('Get Started'),array('controller'=>'pages','action'=>'contact-us'))?>
		</div>
		<div class="btn-black">
			<?php echo $this->Html->link(__('Contac Us'),array('controller'=>'pages','action'=>'contact-us'))?>
		</div>
	</div>
	<div class="col-md-6 col-sm-6 con-clients">
		<?php echo $this->Html->image('find-icon.png')?>
		<h1>CANDIDATES</h1>
		<p>
            Some people are at the top of the ladder, some are in the middle, still more are at the bottom, and a whole lot more don't even know there is a ladder.<br>
            So take step ahead to RG Placement Services
		</p>
		<div class="btn-black left-space">
            <?php echo $this->Html->link(__('Get Started'),array('controller'=>'pages','action'=>'contact-us'))?>
		</div>
		<div class="btn-black">
			<?php echo $this->Html->link(__('Contac Us'),array('controller'=>'pages','action'=>'contact-us'))?>
		</div>
	</div>
</div>
<div class="row">
	<div class="bottom-news">
		<div class="col-md-8 col-sm-6 left-box-news">
			<div class="row">
                <h1>Welcome to <?php echo Configure::read('Site.Name')?></h1>
                <p>
                    RG Placement Services begin to give a fresh start, to rejuvenated, to be introspective about your strengths and talents?  Discover and assess your gaps in knowledge and skill. We are a "You"-centric placement services. 
                </p>
                <p>
                    Our commitment is to fulfill requirements in each & every respect. We never forget the fact that each day confronts a new kind of challenge for companies so, that is why RG Placement is working to resolve the difficulties in getting the right candidate for the required opportunity.
                </p>
                <p>
                    RG Placement recognizes the importance and relevance of the human factor for enhancing an organization's  bottom line. 
                    RG Placement provides career-related consulting services across industries. RG Placement , we go a step forward to ensure that the person not only fits the stated job profile but is the right ‘person fit’ for the organization keeping in mind the organization's work culture. 
                    Our search methodology combined with our capabilities has given our search practice a leadership position in the market place. One of our key business values is speed.
                </p>
				<?php echo $this->Html->link(__('MORE'),array('controller'=>'pages','action'=>'view','about-us'))?>
			</div>
		</div>
		<div class="col-md-4 col-sm-6 latestnews">
			<div class="row">
				<h1>Latest Openings</h1>				
				<div class="scroll-main">
					<?php foreach($latestJob as $k=>$job){?>
					<p><?php echo $job['Job']['title']?>
					<span><?php echo showdatetime($job['Job']['post_time'])?></span></p>
					<div class="btn-more">
						<?php echo $this->Html->link('MORE',array('controller'=>'jobs','action'=>'detail',$job['Job']['id']))?>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
	</div>
</div>