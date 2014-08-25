//require(['fxaClient'], function (FxAccountClient) {

    $(document).ready(function() {

        $("#hello").click(function() {
            var client = new FxAccountClient('https://localhost/owncloud/index.php/apps/firefoxsync/accounts');
//            var client = new FxAccountClient('accounts');
            console.log(client);
            // Sign Up
            client.signUp($("#email").val(), $("#text").val());
            // Sign In
            //client.signIn(email, password);

        });

    });
//});

