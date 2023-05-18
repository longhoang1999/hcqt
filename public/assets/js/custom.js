$.ajaxSetup({
	headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});
$(".drop-icon").click(function() {
	$(".sub-drop-icon").slideToggle(500);
})
$(".noti-parent").click(function() {
	$(".noti-child").slideToggle(500);
})


// $(document).click(function (e) {
//     if (!$(".drop-icon").is(e.target) &&
//         $(".drop-icon").has(e.target).length === 0) {
//         //Đúng là bấm chuột ngoài menu
//          var isopened =
//             $(".sub-drop-icon").css("display");

//         //Ẩn menu đang mở
//          if (isopened == 'block') {
//              $(".sub-drop-icon").slideToggle(500);
//          }
//     }

//     if (!$(".noti-parent").is(e.target) &&
//         $(".noti-parent").has(e.target).length === 0) {
//         //Đúng là bấm chuột ngoài menu
//          var isopened =
//             $(".noti-child").css("display");

//         //Ẩn menu đang mở
//          if (isopened == 'block') {
//              $(".noti-child").slideToggle(500);
//          }
//     }
// });

