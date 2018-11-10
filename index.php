<?php
include 'functions.php';

    $version = '0.56b';
    $obs = htmlspecialchars(stripslashes(trim($_GET['obs'])));
    $obsView = $obs ? $obs : false;
    unset($obs);

    $cfg = cfgRead();
    if ($cfg['last'] != $cfg['card']){ // if cardname changed, use the api scryfall
        $cfg['url'] = 'https://api.scryfall.com/cards/named?format=image&set=&fuzzy='.$cfg['card'];
        $cfg['last'] = $cfg['card'];
        cfgWrite($cfg);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MTG cardSetter <?=$version?></title>
    <link rel="shortcut icon" href="/cards/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
	    $(document).ready(function(){
            $.ajaxSetup({ cache: false });
		    function updatePicture(){
			    $.ajax({
				    type: "POST",
				    url: "ajax.php",
				    data: $("#search_card").serialize(), // serializes the form's elements.
			    }).done(function(data) {
				    $("img#card").attr("src", data);
			    });
		    }
            // Click on button
            $("#search_card").submit(function(e) {
                updatePicture();
                e.preventDefault();
            });
            // Autoupdate
	        setInterval(function(){
                $.getJSON( "settings.json", function( data ) {
                    if (data['url'] != $("img#card").attr("src"))
                        $("img#card").attr("src", data['url']);
                    if (data['copy'] == "true"){
                        $('.img-wrapper').addClass('active');
                        $('.gb').addClass('active');
                    }
                    else{
                        $('.img-wrapper').removeClass('active');
                        $('.gb').removeClass('active');
                    }
                });
	        }, 500);

	        // Autocomplete
            $('#live_update').on('input',function(e){
            	var names =[];
                $.ajax({
                    url: "https://api.scryfall.com/cards/autocomplete?q="+$('#live_update').val(),
                    dataType: "json",
                    success: function(data){
                        $.each( data, function( key, val ) {
                            if (key == 'data'){
                                names = val;
                            }
                        });
	                    $( "#live_update" ).autocomplete({
		                    source: names,
		                    select: function(event, ui) {
			                    $("#live_update").val(ui.item.value);
			                    updatePicture();
		                    }
	                    });
                    }
                });
            });

            $('.gb').click(function (e) {
                e.preventDefault();
                var copy = "false";
                $('.img-wrapper').toggleClass('hide');
                $(this).toggleClass('active');
                if ($(this).hasClass('active')){
                    copy = "true";
                }
                $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    data: {"copy":copy},
                });
            });
        });
    </script>
</head>
<body>
<?if (!$obsView){?>
	<div class="copyrights">
		<h1>MTG cardSetter</h1><a class="hide" href="http://natribu.org/?login=bobrov&pass=WinCondition!123">Push me</a>
		<span>Version: <?=$version?></span>
        <span>Link for OBS: <a class="small" target="_blank" href="?obs=true">http://your.domain/?obs=true</a></span>
		<span>License: Free</span>
        <span>Works on <a target="_blank" class="small" href="https://scryfall.com/docs/api">Scryfall API</a></span>
		<span>Author: Itachi261092</span>
		<span>Telegram: @Itachi261092</span>
		<span>Github: https://github.com/Itachi261092</span>
		<span>VK: http://vk.com/Itachi261092</span>
        <span>Special for <a target="_blank" class="small" href="https://www.youtube.com/channel/UCQwRRLXh4_FMKKpYFlfSHxQ">Wincondition</a></span>
		<span>Copyright &copy; 20!8, All rights reserved</span>
	</div>

    <section class="form">
        <form id="search_card" method="get">
            <label for="cardname">You know what to do:</label>
            <input type="text" id="live_update" name="cardname" value="">
            <button class="red" type="submit"></button>
            <a href="#" class="gb <?if ($cfg['copy']):?>active<?endif;?>"></a>
        </form>
    </section>

<?}?>
<section>
    <div class="<?if ($cfg['copy']):?>active<?endif;?> img-wrapper <?if (!$obsView):?>lower-wrapper<?endif;?>">
        <img id="card" src="<?=$cfg['url']?>">
    </div>
</section>
</body>
</html>