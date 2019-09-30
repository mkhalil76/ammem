$(document).ready(function()
{
  $(".Nsubmit").click(function() {
    $(".Swap-Content").toggle("slow");
    $(".PhoneCode").toggle("slow");


});
$("#wrapper").click( function() {
	$(".menu").toggleClass("close");
});
$(".sub-menu").click(function () {
$(this).toggle();
});

$('.dropdown-menu li ul [data-toggle=dropdown]').on('click', function(event) {
    event.preventDefault();
    event.stopPropagation();
    $(this).parent().siblings().removeClass('open');
    $(this).parent().toggleClass('open');
  });
  $('.dropdown-menu li ul').click(function() {
        $(this).toggle("fast");
    });


// -----------------------------
$(".dropdown li").hover(
  function() {
    $(this).find('ul').slideDown();
  },
  function() {
    $(this).find('ul').slideUp();
  });

// Lest pupub
function showDialog2() {
            $("#dialog1").removeClass("fade").modal("hide");
            $("#dialog2").addClass("fade").modal("show");
        }
        $(function () {
            $("#dialog1").modal("show");
            $("#dialog-ok").on("click", function () {
                showDialog2();
            });
        });


//------- // Lest pupub
$("#signin").on( "click", function() {
        $('#myModal1').modal('hide');
        $('#myModal2').modal('show');
});
//trigger next modal
$("#signin").on( "click", function() {
        $('#myModal2').modal('show');
});

//model creat grop
function showDialog2() {
           $("#dialog1").removeClass("fade").modal("hide");
           $("#dialog2").addClass("fade").modal("show");
       }
       $(function () {
           $("#dialog1").modal("show");
           $("#dialog-ok").on("click", function () {
               showDialog2();
           });
 });
 //set button id on click to hide first modal
 $("#signin").on( "click", function() {
         $('#myModal1').modal('hide');
 });
 //trigger next modal
 $("#signin").on( "click", function() {
         $('#myModal2').modal('show');
 });


 function readURL(input) {
     if (input.files && input.files[0]) {
         var reader = new FileReader();
         reader.onload = function(e) {
             $('#imagePreview').css('background-image', 'url('+e.target.result +')');
             $('#imagePreview').hide();
             $('#imagePreview').fadeIn(650);
         }
         reader.readAsDataURL(input.files[0]);
     }
 }
 $("#imageUpload").change(function() {
     readURL(this);
 });

});
