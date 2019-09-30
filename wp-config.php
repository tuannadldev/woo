<?php
define( 'S3_UPLOADS_BUCKET', 'ecommerce-website-pmc/live' );
define( 'S3_UPLOADS_KEY', 'AKIATTLFZ3GKHVCF6SXX' );
define( 'S3_UPLOADS_SECRET', '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' );
define( 'S3_UPLOADS_REGION', 'ap-southeast-1' ); // the s3 bucket region (excluding the rest of the URL)
define( 'S3_UPLOADS_AUTOENABLE', true );
define('S3_UPLOADS_BUCKET_URL', 'https://image.pharmacity.vn/live');

define('WP_CACHE', true); // Added by WP Rocket
/**
 * Cấu hình cơ bản cho WordPress
 *
 * Trong quá trình cài đặt, file "wp-config.php" sẽ được tạo dựa trên nội dung
 * mẫu của file này. Bạn không bắt buộc phải sử dụng giao diện web để cài đặt,
 * chỉ cần lưu file này lại với tên "wp-config.php" và điền các thông tin cần thiết.
 *
 * File này chứa các thiết lập sau:
 *
 * * Thiết lập MySQL
 * * Các khóa bí mật
 * * Tiền tố cho các bảng database
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Thiết lập MySQL - Bạn có thể lấy các thông tin này từ host/server ** //
define( 'DB_NAME', 'woo_db' );

/** Username của database */
define( 'DB_USER', 'woomaster' );

/** Mật khẩu của database */
define( 'DB_PASSWORD', 'Pharmacitywoo' );

/** Hostname của database */
define( 'DB_HOST', 'woo.cfme3xwcf2ka.ap-southeast-1.rds.amazonaws.com' );

/** Database charset sử dụng để tạo bảng database. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Kiểu database collate. Đừng thay đổi nếu không hiểu rõ. */
define('DB_COLLATE', '');
define(‘DISABLE_WP_CRON’, true);
/**#@+
 * Khóa xác thực và salt.
 *
 * Thay đổi các giá trị dưới đây thành các khóa không trùng nhau!
 * Bạn có thể tạo ra các khóa này bằng công cụ
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Bạn có thể thay đổi chúng bất cứ lúc nào để vô hiệu hóa tất cả
 * các cookie hiện có. Điều này sẽ buộc tất cả người dùng phải đăng nhập lại.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '- :eDY8NmHx(C]Lf>$o-9K7=4EABke_e1}Z<vjwuugc;?r7-H,}Ib^cd,kicL{Oi' );
define( 'SECURE_AUTH_KEY',  'J*W&gPojT^<[*,b!#D-#Q;>;;E?yUf&m`SSJeNEZI9JU)hk1}FVS3dAzl~-U5fv{' );
define( 'LOGGED_IN_KEY',    'cjGNq=}|ovcECql/ikAGs1U)s~(&9U|Q_[]$R{N-QpF#=CQegT-?pKk:2auAjtDf' );
define( 'NONCE_KEY',        'a7HL! W6S%bp<GHXX?Fh5WTS:,,FSw]PXNBxZ#VM U%sHb7M6iX5lPkW]u_=p{XZ' );
define( 'AUTH_SALT',        'Hqw.pxZj-n3}[rQhYj2vf7h0HP:RO44v4O+6AesOq}hW4YET9y=2h4&NW6jJA{]*' );
define( 'SECURE_AUTH_SALT', 'X0)FH|a?%x56h<$LEZ4:xJ!wg=mU9oRw@=:4jg f]9%e4J9}<M$RSTR}^hhbOJ$(' );
define( 'LOGGED_IN_SALT',   'fZY@LWi~qIJc@NWU#:]1+58eiJ>mDz4tG1Ad V^e!vST$9l;FLkLJ?fdabxyleIR' );
define( 'NONCE_SALT',       '1tF k,rGqv7XhO>CU2_ZPc&yL[Z>L)0fmn ^N6Q&M>oDv;y&z?1Bklm2d_VSB%Nu' );
define( 'WPCF7_UPLOADS_TMP_DIR', '/var/www/pmc/prescription' );
/**#@-*/

/**
 * Tiền tố cho bảng database.
 *
 * Đặt tiền tố cho bảng giúp bạn có thể cài nhiều site WordPress vào cùng một database.
 * Chỉ sử dụng số, ký tự và dấu gạch dưới!
 */
$table_prefix = 'pharm_';
define('WP_HOME','https://www.pharmacity.vn');
define('WP_SITEURL','https://www.pharmacity.vn');
/**
 * Dành cho developer: Chế độ debug.
 *
 * Thay đổi hằng số này thành true sẽ làm hiện lên các thông báo trong quá trình phát triển.
 * Chúng tôi khuyến cáo các developer sử dụng WP_DEBUG trong quá trình phát triển plugin và theme.
 *
 * Để có thông tin về các hằng số khác có thể sử dụng khi debug, hãy xem tại Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('ALLOW_UNFILTERED_UPLOADS', true);
define('WP_DEBUG', false);
define('WP_MEMORY_LIMIT', '240M');
/* Đó là tất cả thiết lập, ngưng sửa từ phần này trở xuống. Chúc bạn viết blog vui vẻ. */

/** Đường dẫn tuyệt đối đến thư mục cài đặt WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Thiết lập biến và include file. */
require_once(ABSPATH . 'wp-settings.php');
