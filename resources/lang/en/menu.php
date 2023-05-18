<?php
	
	return array(
		0				=> 'Home',
		1				=> 'Thường trực',
		'1_list'		=> array(
			9 			=> 'QL bộ tiêu chuẩn',
			10 			=> 'QL danh mục',
			11 			=> 'Đối sách',
		),
		2 				=> 'Trao đổi thông tin',
		'2_list' 		=> array(
			12 			=> 'Bản tin',
			13 			=> 'Chat',
		),
		3 				=> 'Đảm bảo chất lượng',
		'3_list' 		=> array(
			14 			=> 'Lập kế hoạch',
			15 			=> 'Cập nhật hoạt động',
			16 			=> 'Cập nhật minh chứng',
			17 			=> 'KTMC Theo hoạt động',
			18 			=> 'KH hoạt động',
		),
		4 				=> 'So chuẩn',
		'4_list' 		=> array(
			19 			=> 'Lập kế hoạch',
			20 			=> 'Thực hiện so chuẩn',
			21 			=> 'Tổng hợp kết quả',
			22 			=> 'Yêu cầu cải tiến',
		),
		5 				=> 'Đối sách',
		'5_list' 		=> array(
			23 			=> 'Lập kế hoạch',
			24 			=> 'Thực hiện đối sách',
			25 			=> 'Tổng hợp kết quả',
			26 			=> 'Yêu cầu cải tiến',
		),
		6 				=> 'Tự đánh giá',
		'6_list' 		=> array(
			27 			=> 'Lập kế hoạch',
			28 			=> 'Chuẩn bị đánh giá',
			29 			=> 'Báo cáo tự đánh giá',
			30 			=> 'Nhận xét báo cáo',
			31 			=> 'Hoàn thiện báo cáo',
		),
		7 				=> 'Đánh giá ngoài',
		'7_list' 		=> array(
			32 			=> 'Lập kế hoạch ĐGN',
			33 			=> 'Báo cáo TĐG',
		),
		8 				=> 'Tổng hợp',
		'8_list' 		=> array(
			34 			=> 'Đảm bảo chất lượng',
			35 			=> 'Báo cáo tiến độ',
			36 			=> 'DS Báo cáo TĐG',
			37 			=> 'Báo cáo nhận xét',
		),
		'9_list'		=> array(
			38 			=> 'QL tiêu chuẩn, tiêu chí',
			39 			=> 'Mốc chuẩn',
			40 			=> 'Gợi ý hướng dẫn',
			41 			=> 'Minh chứng tối thiểu'
		),
		'10_list' 		=> array(
			42 			=> 'Lĩnh vực',
			43 			=> 'QL đơn vị',
			44 			=> 'QL CTĐT',
			45 			=> 'QL Nhân sự',
			46 			=> 'QL Link báo cáo ngoài',
		),
		'11_list' 		=> array(
			47 			=> 'Tiêu chí đối sách',
			48 			=> 'Đối tượng đối sách',
		),
		'1_list_parent' => array(
			9 			=> route('admin.function_one.setstandard.index'),
			10 			=> route('admin.function_one.manacategory.index'),
			11 			=> route('admin.function_one.manastrategy.index'),
		),
		'1_list_child' 	=> array(
			38 			=> route('admin.function_one.setstandard.criteria'),
			39 			=> route('admin.function_one.setstandard.benchmark'),
			40 			=> route('admin.function_one.setstandard.suggestions'),
			41 			=> route('admin.function_one.setstandard.minimum'),

			42 			=> route('admin.function_one.manacategory.field'),
			43 			=> route('admin.function_one.manacategory.unit'),
			44 			=> route('admin.function_one.manacategory.ctdt'),
			45 			=> route('admin.function_one.manacategory.human'),
			46 			=> route('admin.function_one.manacategory.linkreport'),

			47 			=> route('admin.function_one.manastrategy.stracriteria'),
			48 			=> route('admin.function_one.manastrategy.strasubject'),
		),

	);