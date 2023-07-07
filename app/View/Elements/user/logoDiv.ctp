<label class="form-group clearBoth col-md-12 no-padding" style="margin-bottom: 10px">
    <?php
    echo $logoTitle;

    ?>
</label>
<div class="form-group clearBoth" style="margin-bottom: 40px">
    <?php
    if (!empty($id)) {
        echo "<div id='UserProfileImageId' class='col-md-3'>" . $this->Html->image(getUserPhoto($id, $photo), array('class' => 'thumbnail img-responsive')) . "</div>";
    }

    ?>
    <?php
    echo $this->Form->input('photo', array('label' => false, 'type' => 'file', 'placeholder' => 'Photo', 'required' => false, 'class' => ''));

    ?>
</div>