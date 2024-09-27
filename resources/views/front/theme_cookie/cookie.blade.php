<style type="text/css">
    .cookies-infobar {
        background-color: #fff;
        color: rgb(43, 43, 43);
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 20px;
        box-sizing: border-box;
        position: fixed;
        width: 70%;
        bottom: 15px;
        /*text-align: center;*/
        z-index: 999999;
        left: 15%;
        /*box-shadow: 5px 10px #888888;*/
    }
    .cookies-infobar_wrapper {
		text-align: center;
		/*margin-top: 35px;*/
		/*margin-bottom: 10px;*/
    }
    .cookies-infobar a {
		color: inherit;
    }
    .font-weight-bold{
        font-weight: bold;
        line-height: 1;
    }
    .text-white-btn{
        color: #ffffff !important;
    }
    .f-15{
        font-size: 15px !important;
    }
    .f-16{
        font-size: 16px !important;
    }
    .f-18{
        font-size: 18px !important;
    }
    .f-20{
        font-size: 18px !important;
    }
    .pb-2{
        padding-bottom: 20px;
    }

    @media only screen and (max-width: 600px) {
        .cookies-infobar {
            width: 100%;
            left: 0%;
        }
    }
    @media only screen and (max-width: 768px) {
        .cookies-infobar {
            width: 90%;
            left: 5%;
        }
    }

</style>

<?php 
    session_start();
	$policy_result = DB::table('policy_and_terms')
             ->select('*')
             ->where('type', '=', 1)
             ->get();


    if(isset($_SESSION["cookie_active"])){
        $cookie_active = $_SESSION["cookie_active"];
    }else{
        $cookie_active = "";
    }

    $cookie_hidden = "";
    if($cookie_active == 1 || count($policy_result) <= 0){
        $cookie_hidden = "hidden";
    }

?>

<div class="cookies-infobar" <?php echo $cookie_hidden?>>
    <p class="text-left font-weight-bold pt-2">{{__('common.common_use_cookie')}}</p>
    <p class="text-left f-18">{{__('common.common_detail_cookie')}}</p>
    <div class="cookies-infobar_wrapper">
        <a class="btn btn-info" onclick="activeCookie();">
            {{__('common.common_active_btn')}}
        </a>
        <a href="<?php echo route('privacy-and-policy');?>" target="_blank" class="btn btn-info">
            {{__('common.common_detail_btn')}}
        </a>
    </div>
</div>

<script>
    function activeCookie(){
        $.ajax({
            type: 'GET',
            url: "<?php echo route('saveCookie');?>",
            success: function(json){
                var json = JSON.parse(json);
                console.log(json);
                if(json == 1){
                    $(".cookies-infobar").hide();
                }
            }
        });
    }

</script>