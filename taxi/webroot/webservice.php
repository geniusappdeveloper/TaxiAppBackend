<?php
$url = str_replace('webservice.php', '', 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
?>

REGISTER
<form action = "<?php echo $url; ?>userServices/index" method="POST" style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 20px;">
    first Name <input name="first_name" type="text" /><br/>
    Last Name <input name="last_name" type="text" /><br/>
    Email <input name="email" type="email" /><br/>
    Password<input name="password" type="password" /><br/>
    Country Code<input name="country_code" type="text" /><br/>
    Phone Number<input name="phone_number" type="text" /><br/>
    User Type <select name ="user_type"><br/><br/>
        <option value="U">User</option>
        <option value="C">Company</option></select>
        <input name="method" type="hidden" value="register" />
        <input type="submit" value="Signup">
</form>
