<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/30/19
 * Time: 6:44 PM
 */
class Get_State{

    public static function get_city_code_by_name($city_name){

        $VN_state = array(
            "HANOI" => "Thành Phố Hà Nội",
            "HOCHIMINH" => "Thành Phố Hồ Chí Minh",
            "ANGIANG" => "Tỉnh An Giang",
            "BACGIANG" => "Tỉnh Bắc Giang",
            "BACKAN" => "Tỉnh Bắc Kạn",
            "BACLIEU" => "Tỉnh Bạc Liêu",
            "BACNINH" => "Tỉnh Bắc Ninh",
            "BARIAVUNGTAU" => "Tỉnh Bà Rịa - Vũng Tàu",
            "BENTRE" => "Tỉnh Bến Tre",
            "BINHDINH" => "Tỉnh Bình Định",
            "BINHDUONG" => "Tỉnh Bình Dương",
            "BINHPHUOC" => "Tỉnh Bình Phước",
            "BINHTHUAN" => "Tỉnh Bình Thuận",
            "CAMAU" => "Tỉnh Cà Mau",
            "CANTHO" => "Thành Phố Cần Thơ",
            "CAOBANG" => "Tỉnh Cao Bằng",
            "DAKLAK" => "Tỉnh Đắk Lắk",
            "DAKNONG" => "Tỉnh Đắk Nông",
            "DANANG" => "Thành Phố Đà Nẵng",
            "DIENBIEN" => "Tỉnh Điện Biên",
            "DONGNAI" => "Tỉnh Đồng Nai",
            "DONGTHAP" => "Tỉnh Đồng Tháp",
            "GIALAI" => "Tỉnh Gia Lai",
            "HAGIANG" => "Tỉnh Hà Giang",
            "HAIDUONG" => "Tỉnh Hải Dương",
            "HAIPHONG" => "Thành Phố Hải Phòng",
            "HANAM" => "Tỉnh Hà Nam",
            "HATINH" => "Tỉnh Hà Tĩnh",
            "HAUGIANG" => "Tỉnh Hậu Giang",
            "HOABINH" => "Tỉnh Hoà Bình",
            "HUNGYEN" => "Tỉnh Hưng Yên",
            "KHANHHOA" => "Tỉnh Khánh Hòa",
            "KIENGIANG" => "Tỉnh Kiên Giang",
            "KONTUM" => "Tỉnh Kon Tum",
            "LAICHAU" => "Tỉnh Lai Châu",
            "LAMDONG" => "Tỉnh Lâm Đồng",
            "LANGSON" => "Tỉnh Lạng Sơn",
            "LAOCAI" => "Tỉnh Lào Cai",
            "LONGAN" => "Tỉnh Long An",
            "NAMDINH" => "Tỉnh Nam Định",
            "NGHEAN" => "Tỉnh Nghệ An",
            "NINHBINH" => "Tỉnh Ninh Bình",
            "NINHTHUAN" => "Tỉnh Ninh Thuận",
            "PHUTHO" => "Tỉnh Phú Thọ",
            "PHUYEN" => "Tỉnh Phú Yên",
            "QUANGBINH" => "Tỉnh Quảng Bình",
            "QUANGNAM" => "Tỉnh Quảng Nam",
            "QUANGNGAI" => "Tỉnh Quảng Ngãi",
            "QUANGNINH" => "Tỉnh Quảng Ninh",
            "QUANGTRI" => "Tỉnh Quảng Trị",
            "SOCTRANG" => "Tỉnh Sóc Trăng",
            "SONLA" => "Tỉnh Sơn La",
            "TAYNINH" => "Tỉnh Tây Ninh",
            "THAIBINH" => "Tỉnh Thái Bình",
            "THAINGUYEN" => "Tỉnh Thái Nguyên",
            "THANHHOA" => "Tỉnh Thanh Hóa",
            "THUATHIENHUE" => "Tỉnh Thừa Thiên Huế",
            "TIENGIANG" => "Tỉnh Tiền Giang",
            "TRAVINH" => "Tỉnh Trà Vinh",
            "TUYENQUANG" => "Tỉnh Tuyên Quang",
            "VINHLONG" => "Tỉnh Vĩnh Long",
            "VINHPHUC" => "Tỉnh Vĩnh Phúc",
            "YENBAI" => "Tỉnh Yên Bái",
        );

        return array_search($city_name,$VN_state);
    }
}
