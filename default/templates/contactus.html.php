<h1>Contact Us</h1>

<?php if(isset($errors)):?>
    <?php foreach ($errors as $error):?>
        <p><?=$error?></p>
    <?php endforeach;?>
<?php endif;?>
<p>Submit an Enquiry to contact us with the form below.</p>
<form action="" method="POST">

    <label>Name</label>
    <input type="text" name="enquiry[name]" placeholder="Enter Name">

    <label>Email</label>
    <input type="text" name="enquiry[email]" placeholder="Enter Email">

    <label>Telephone</label>
    <input type="text" name="enquiry[telephone]" placeholder="Enter telephone">

    <label>Enter enquiry here....</label>
    <textarea max="5000" name="enquiry[enquiry_details]" placeholder="Enter enquiry here....."></textarea>

    <input type="submit" value="Send"/>

</form>

