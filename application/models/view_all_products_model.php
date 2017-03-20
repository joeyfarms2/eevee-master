<?php
class View_all_products_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("view_all_products");
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}

				$title = get_array_value($row,"title","N/A");
				$row["title_short"] = getShortString($title, CONST_TITLE_SHORT_CHAR);

				$upload_path = get_array_value($row,"upload_path","");
				$cover_image_file_type = get_array_value($row,"cover_image_file_type","");

				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-thumb'.$cover_image_file_type;
				$image = get_image($image_path,"thumb");
				$row["cover_image_thumb_path"] = $image_path;
				$row["cover_image_thumb"] = $image;
				
				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-cover'.$cover_image_file_type;
				$image = get_image($image_path, "detail");
				$row["cover_image_detail_path"] = $image_path;
				$row["cover_image_detail"] = $image;
				
				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-related'.$cover_image_file_type;
				$image = get_image($image_path,"related");
				$row["cover_image_related_path"] = $image_path;
				$row["cover_image_related"] = $image;
				
				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-small'.$cover_image_file_type;
				$image = get_image($image_path,"small");
				$row["cover_image_small_path"] = $image_path;
				$row["cover_image_small"] = $image;
				
				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-ori'.$cover_image_file_type;
				$image = get_image($image_path,"","off");
				$row["cover_image_ipad_path"] = $image_path;
				$row["cover_image_ipad"] = $image;
							
				$created_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"created_date",""),"");
				$row["created_date_txt"] = $created_date_txt;
				$updated_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"updated_date",""),"");
				$row["updated_date_txt"] = $updated_date_txt;
				
				$publish_date_txt = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"publish_date",""),"");
				$row["publish_date_txt"] = $publish_date_txt;
				
				$publish_date_short_txt = get_datetime_pattern("dmy_EN_SHORT",get_array_value($row,"publish_date",""),"");
				$row["publish_date_short_txt"] = $publish_date_short_txt;

				$result[] = $row;
			}
		}
		return $result;
	}

	function update_data(){
		// header('Content-Type: text/html; charset=utf-8');
		$_sql = 'TRUNCATE TABLE view_all_products';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, cid, user_owner_aid, product_main_aid, product_type_aid, publisher_aid, title, author, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, uri, tag, total_copy, total_view, total_download, total_read, total_rental, best_price, has_ebook, has_license, reward_point, review_point, created_date, created_by, updated_date, updated_by, cover_image, thumbnail_image, large_image';

		$_sql = '
		INSERT INTO view_all_products
		('.$columns.')
		SELECT '.$columns.'
		FROM book 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_products
		('.$columns.')
		SELECT '.$columns.'
		FROM magazine 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_products
		('.$columns.')
		SELECT '.$columns.'
		FROM vdo 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = 'TRUNCATE TABLE view_all_products_with_detail';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, cid, user_owner_aid, product_main_aid, product_type_aid, publisher_aid, title, author, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, uri, tag, total_copy, total_view, total_download, total_read, total_rental, best_price, has_ebook, has_license, reward_point, review_point, created_date, created_by, updated_date, updated_by, product_type_cid, publisher_name, product_main_name, product_main_url, cover_image, thumbnail_image, large_image';

		$_sql = '
		INSERT INTO view_all_products_with_detail
		('.$columns.')
		SELECT vap.aid, vap.cid, vap.user_owner_aid, vap.product_main_aid, vap.product_type_aid, vap.publisher_aid, vap.title, vap.author, vap.status, vap.weight, vap.is_new, vap.is_recommended, vap.is_home, vap.publish_date, vap.expired_date, vap.category, vap.upload_path, vap.cover_image_file_type, vap.uri, vap.tag, vap.total_copy, vap.total_view, vap.total_download, vap.total_read, vap.total_rental, vap.best_price, vap.has_ebook, vap.has_license, vap.reward_point, vap.review_point, vap.created_date, vap.created_by, vap.updated_date, vap.updated_by, pt.cid as product_type_cid, pb.name as publisher_name, pm.name as product_main_name, pm.url as product_main_url, vap.cover_image as cover_image, vap.thumbnail_image as thumbnail_image, vap.large_image as large_image  
		FROM view_all_products vap
		LEFT JOIN product_type pt ON pt.aid = vap.product_type_aid 
		LEFT JOIN product_main pm ON pm.aid = vap.product_main_aid 
		LEFT JOIN publisher pb ON pb.aid = vap.publisher_aid 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		// Copies
		$_sql = 'TRUNCATE TABLE view_all_product_copies';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, cid, barcode, nonconsume_identifier, user_owner_aid, parent_aid, product_type_aid, product_type_minor_aid, copy_title, publish_date, expired_date, weight, upload_path, file_upload, status, no_1, no_2, no_3, no_4, call_number, cover_price, source, note_1, note_2, note_3, type, possession, is_license, is_ebook, ebook_concurrence, digital_price, digital_point, paper_price, paper_point, in_stock, rental_period, rental_fee, rental_fee_point, rental_fine_fee, shelf_status, shelf_name, created_date, created_by, updated_date, updated_by';

		$_sql = '
		INSERT INTO view_all_product_copies
		('.$columns.')
		SELECT '.$columns.'
		FROM book_copy 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_copies
		('.$columns.')
		SELECT '.$columns.'
		FROM magazine_copy 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_copies
		('.$columns.')
		SELECT '.$columns.'
		FROM vdo_copy 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = 'TRUNCATE TABLE view_all_product_copies_with_detail';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		$columns = 'aid, cid, barcode, nonconsume_identifier, user_owner_aid, parent_aid, product_type_aid, product_type_minor_aid, copy_title, publish_date, expired_date, weight, upload_path, file_upload, status, no_1, no_2, no_3, no_4, call_number, cover_price, source, note_1, note_2, note_3, type, possession, is_license, is_ebook, ebook_concurrence, digital_price, digital_point, paper_price, paper_point, in_stock, rental_period, rental_fee, rental_fee_point, rental_fine_fee, shelf_status, shelf_name, created_date, created_by, updated_date, updated_by, product_type_cid, product_type_name, parent_cid, parent_title, parent_author, parent_status, parent_weight, parent_upload_path, parent_cover_image_file_type, product_main_aid, category';

		$_sql = '
		INSERT INTO view_all_product_copies_with_detail
		('.$columns.')
		SELECT vapc.aid, vapc.cid, vapc.barcode, vapc.nonconsume_identifier, vapc.user_owner_aid, vapc.parent_aid, vapc.product_type_aid, vapc.product_type_minor_aid, vapc.copy_title, vapc.publish_date, vapc.expired_date, vapc.weight, vapc.upload_path, vapc.file_upload, vapc.status, vapc.no_1, vapc.no_2, vapc.no_3, vapc.no_4, vapc.call_number, vapc.cover_price, vapc.source, vapc.note_1, vapc.note_2, vapc.note_3, vapc.type, vapc.possession, vapc.is_license, vapc.is_ebook, vapc.ebook_concurrence, vapc.digital_price, vapc.digital_point, vapc.paper_price, vapc.paper_point, vapc.in_stock, vapc.rental_period, vapc.rental_fee, vapc.rental_fee_point, vapc.rental_fine_fee, vapc.shelf_status, vapc.shelf_name, vapc.created_date, vapc.created_by, vapc.updated_date, vapc.updated_by, pt.cid as product_type_cid, pt.name as product_type_name, vap.cid as parent_cid, vap.title as parent_title, vap.author as parent_author, vap.status as parent_status, vap.weight as parent_weight, vap.upload_path as parent_upload_path, vap.cover_image_file_type as parent_cover_image_file_type, vap.product_main_aid, vap.category
		FROM view_all_product_copies vapc
		LEFT JOIN view_all_products vap ON vap.aid = vapc.parent_aid AND vap.product_type_aid = vapc.product_type_aid 
		LEFT JOIN product_type pt ON pt.aid = vapc.product_type_aid 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		// Fields
		$_sql = 'TRUNCATE TABLE view_all_product_fields';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'parent_aid, sequence, product_type_aid, product_main_field_aid, name, user_owner_aid, tag, ind1_cd, ind2_cd, subfield_cd, field_data, created_date, created_by, updated_date, updated_by';

		$_sql = '
		INSERT INTO view_all_product_fields
		('.$columns.')
		SELECT '.$columns.'
		FROM book_field 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_fields
		('.$columns.')
		SELECT '.$columns.'
		FROM magazine_field 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_fields
		('.$columns.')
		SELECT '.$columns.'
		FROM vdo_field 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = 'TRUNCATE TABLE view_all_product_fields_with_detail';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		$columns = 'sequence, tag, field_data, product_main_field_aid, aid, cid, user_owner_aid, product_main_aid, product_type_aid, publisher_aid, title, author, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, uri, product_tag, total_copy, total_view, total_download, total_read, total_rental, best_price, has_ebook, has_license, reward_point, review_point, created_date, created_by, updated_date, updated_by, product_type_cid, publisher_name, tag_weight';
		$columns = 'sequence, tag, field_data, product_main_field_aid, aid, cid, user_owner_aid, product_main_aid, product_type_aid, publisher_aid, title, author, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, uri, product_tag, total_copy, total_view, total_download, total_read, total_rental, best_price, has_ebook, has_license, reward_point, review_point, created_date, created_by, updated_date, updated_by, product_type_cid, publisher_name, tag_weight';

		$_sql = '
		INSERT INTO view_all_product_fields_with_detail
		('.$columns.')
		SELECT vapf.sequence, vapf.tag, vapf.field_data, vapf.product_main_field_aid , vap.aid, vap.cid, vap.user_owner_aid, vap.product_main_aid, vap.product_type_aid, vap.publisher_aid, vap.title, vap.author, vap.status, vap.weight, vap.is_new, vap.is_recommended, vap.is_home, vap.publish_date, vap.expired_date, vap.category, vap.upload_path, vap.cover_image_file_type, vap.uri, vap.tag, vap.total_copy, vap.total_view, vap.total_download, vap.total_read, vap.total_rental, vap.best_price, vap.has_ebook, vap.has_license, vap.reward_point, vap.review_point, vap.created_date, vap.created_by, vap.updated_date, vap.updated_by, vap.product_type_cid, vap.publisher_name, IFNULL(so.weight,999999) as tag_weight
		FROM view_all_product_fields vapf
		LEFT JOIN view_all_products_with_detail vap ON vap.aid = vapf.parent_aid AND vap.product_type_aid = vapf.product_type_aid 
		LEFT JOIN search_order so ON so.tag = vapf.tag 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		// Tag
		$_sql = 'TRUNCATE TABLE view_all_product_tags';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'parent_aid, product_type_aid, user_owner_aid, tag';

		$_sql = '
		INSERT INTO view_all_product_tags
		('.$columns.')
		SELECT '.$columns.'
		FROM book_tag 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_tags
		('.$columns.')
		SELECT '.$columns.'
		FROM magazine_tag 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_tags
		('.$columns.')
		SELECT '.$columns.'
		FROM vdo_tag 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = 'TRUNCATE TABLE view_all_product_tags_with_detail';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		$columns = 'tag, aid, cid, product_type_aid, product_main_aid, title, author, publisher_aid, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, total_copy, total_view, total_download, total_read, review_point, best_price, has_ebook, has_license, created_date, created_by, updated_date, updated_by';

		$_sql = '
		INSERT INTO view_all_product_tags_with_detail
		('.$columns.')
		SELECT vapt.tag,vap.aid, vap.cid, vap.product_type_aid, vap.product_main_aid, vap.title, vap.author, vap.publisher_aid, vap.status, vap.weight, vap.is_new, vap.is_recommended, vap.is_home, vap.publish_date, vap.expired_date, vap.category, vap.upload_path, vap.cover_image_file_type, vap.total_copy, vap.total_view, vap.total_download, vap.total_read, vap.review_point, vap.best_price, vap.has_ebook, vap.has_license, vap.created_date, vap.created_by, vap.updated_date, vap.updated_by
		FROM view_all_product_tags vapt
		LEFT JOIN view_all_products vap ON vap.aid = vapt.parent_aid AND vap.product_type_aid = vapt.product_type_aid 
		LIMIT 100000;
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);
	
	}

	function update_data_save(){
		// header('Content-Type: text/html; charset=utf-8');
		$_sql = 'TRUNCATE TABLE view_all_products';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, cid, user_owner_aid, product_main_aid, product_type_aid, publisher_aid, title, author, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, uri, tag, total_copy, total_view, total_download, total_read, total_rental, best_price, has_ebook, has_license, reward_point, review_point, created_date, created_by, updated_date, updated_by, cover_image, thumbnail_image, large_image';

		$_sql = '
		INSERT INTO view_all_products
		('.$columns.')
		SELECT '.$columns.'
		FROM book 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_products
		('.$columns.')
		SELECT '.$columns.'
		FROM magazine 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_products
		('.$columns.')
		SELECT '.$columns.'
		FROM vdo 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = 'TRUNCATE TABLE view_all_products_with_detail';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, cid, user_owner_aid, product_main_aid, product_type_aid, publisher_aid, title, author, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, uri, tag, total_copy, total_view, total_download, total_read, total_rental, best_price, has_ebook, has_license, reward_point, review_point, created_date, created_by, updated_date, updated_by, product_type_cid, publisher_name, product_main_name, product_main_url, cover_image, thumbnail_image, large_image';

		$_sql = '
		INSERT INTO view_all_products_with_detail
		('.$columns.')
		SELECT vap.aid, vap.cid, vap.user_owner_aid, vap.product_main_aid, vap.product_type_aid, vap.publisher_aid, vap.title, vap.author, vap.status, vap.weight, vap.is_new, vap.is_recommended, vap.is_home, vap.publish_date, vap.expired_date, vap.category, vap.upload_path, vap.cover_image_file_type, vap.uri, vap.tag, vap.total_copy, vap.total_view, vap.total_download, vap.total_read, vap.total_rental, vap.best_price, vap.has_ebook, vap.has_license, vap.reward_point, vap.review_point, vap.created_date, vap.created_by, vap.updated_date, vap.updated_by, pt.cid as product_type_cid, pb.name as publisher_name, pm.name as product_main_name, pm.url as product_main_url, vap.cover_image as cover_image, vap.thumbnail_image as thumbnail_image, vap.large_image as large_image  
		FROM view_all_products vap
		LEFT JOIN product_type pt ON pt.aid = vap.product_type_aid 
		LEFT JOIN product_main pm ON pm.aid = vap.product_main_aid 
		LEFT JOIN publisher pb ON pb.aid = vap.publisher_aid 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		// Copies
		$_sql = 'TRUNCATE TABLE view_all_product_copies';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, cid, barcode, nonconsume_identifier, user_owner_aid, parent_aid, product_type_aid, product_type_minor_aid, copy_title, publish_date, expired_date, weight, upload_path, file_upload, status, no_1, no_2, no_3, no_4, call_number, cover_price, source, note_1, note_2, note_3, type, possession, is_license, is_ebook, ebook_concurrence, digital_price, digital_point, paper_price, paper_point, in_stock, rental_period, rental_fee, rental_fee_point, rental_fine_fee, shelf_status, shelf_name, created_date, created_by, updated_date, updated_by';

		$_sql = '
		INSERT INTO view_all_product_copies
		('.$columns.')
		SELECT '.$columns.'
		FROM book_copy 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_copies
		('.$columns.')
		SELECT '.$columns.'
		FROM magazine_copy 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_copies
		('.$columns.')
		SELECT '.$columns.'
		FROM vdo_copy 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = 'TRUNCATE TABLE view_all_product_copies_with_detail';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		$columns = 'aid, cid, barcode, nonconsume_identifier, user_owner_aid, parent_aid, product_type_aid, product_type_minor_aid, copy_title, publish_date, expired_date, weight, upload_path, file_upload, status, no_1, no_2, no_3, no_4, call_number, cover_price, source, note_1, note_2, note_3, type, possession, is_license, is_ebook, ebook_concurrence, digital_price, digital_point, paper_price, paper_point, in_stock, rental_period, rental_fee, rental_fee_point, rental_fine_fee, shelf_status, shelf_name, created_date, created_by, updated_date, updated_by, product_type_cid, product_type_name, parent_cid, parent_title, parent_author, parent_status, parent_weight, parent_upload_path, parent_cover_image_file_type, product_main_aid, category';

		$_sql = '
		INSERT INTO view_all_product_copies_with_detail
		('.$columns.')
		SELECT vapc.aid, vapc.cid, vapc.barcode, vapc.nonconsume_identifier, vapc.user_owner_aid, vapc.parent_aid, vapc.product_type_aid, vapc.product_type_minor_aid, vapc.copy_title, vapc.publish_date, vapc.expired_date, vapc.weight, vapc.upload_path, vapc.file_upload, vapc.status, vapc.no_1, vapc.no_2, vapc.no_3, vapc.no_4, vapc.call_number, vapc.cover_price, vapc.source, vapc.note_1, vapc.note_2, vapc.note_3, vapc.type, vapc.possession, vapc.is_license, vapc.is_ebook, vapc.ebook_concurrence, vapc.digital_price, vapc.digital_point, vapc.paper_price, vapc.paper_point, vapc.in_stock, vapc.rental_period, vapc.rental_fee, vapc.rental_fee_point, vapc.rental_fine_fee, vapc.shelf_status, vapc.shelf_name, vapc.created_date, vapc.created_by, vapc.updated_date, vapc.updated_by, pt.cid as product_type_cid, pt.name as product_type_name, vap.cid as parent_cid, vap.title as parent_title, vap.author as parent_author, vap.status as parent_status, vap.weight as parent_weight, vap.upload_path as parent_upload_path, vap.cover_image_file_type as parent_cover_image_file_type, vap.product_main_aid, vap.category
		FROM view_all_product_copies vapc
		LEFT JOIN view_all_products vap ON vap.aid = vapc.parent_aid AND vap.product_type_aid = vapc.product_type_aid 
		LEFT JOIN product_type pt ON pt.aid = vapc.product_type_aid 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		// Fields
		$_sql = 'TRUNCATE TABLE view_all_product_fields';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'parent_aid, sequence, product_type_aid, product_main_field_aid, name, user_owner_aid, tag, ind1_cd, ind2_cd, subfield_cd, field_data, created_date, created_by, updated_date, updated_by';

		$_sql = '
		INSERT INTO view_all_product_fields
		('.$columns.')
		SELECT '.$columns.'
		FROM book_field 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_fields
		('.$columns.')
		SELECT '.$columns.'
		FROM magazine_field 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_fields
		('.$columns.')
		SELECT '.$columns.'
		FROM vdo_field 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = 'TRUNCATE TABLE view_all_product_fields_with_detail';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		$columns = 'sequence, tag, field_data, product_main_field_aid, aid, cid, user_owner_aid, product_main_aid, product_type_aid, publisher_aid, title, author, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, uri, product_tag, total_copy, total_view, total_download, total_read, total_rental, best_price, has_ebook, has_license, reward_point, review_point, created_date, created_by, updated_date, updated_by, product_type_cid, publisher_name, tag_weight';
		$columns = 'sequence, tag, field_data, product_main_field_aid, aid, cid, user_owner_aid, product_main_aid, product_type_aid, publisher_aid, title, author, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, uri, product_tag, total_copy, total_view, total_download, total_read, total_rental, best_price, has_ebook, has_license, reward_point, review_point, created_date, created_by, updated_date, updated_by, product_type_cid, publisher_name, tag_weight';

		$_sql = '
		INSERT INTO view_all_product_fields_with_detail
		('.$columns.')
		SELECT vapf.sequence, vapf.tag, vapf.field_data, vapf.product_main_field_aid , vap.aid, vap.cid, vap.user_owner_aid, vap.product_main_aid, vap.product_type_aid, vap.publisher_aid, vap.title, vap.author, vap.status, vap.weight, vap.is_new, vap.is_recommended, vap.is_home, vap.publish_date, vap.expired_date, vap.category, vap.upload_path, vap.cover_image_file_type, vap.uri, vap.tag, vap.total_copy, vap.total_view, vap.total_download, vap.total_read, vap.total_rental, vap.best_price, vap.has_ebook, vap.has_license, vap.reward_point, vap.review_point, vap.created_date, vap.created_by, vap.updated_date, vap.updated_by, vap.product_type_cid, vap.publisher_name, IFNULL(so.weight,999999) as tag_weight
		FROM view_all_product_fields vapf
		LEFT JOIN view_all_products_with_detail vap ON vap.aid = vapf.parent_aid AND vap.product_type_aid = vapf.product_type_aid 
		LEFT JOIN search_order so ON so.tag = vapf.tag 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		// Tag
		$_sql = 'TRUNCATE TABLE view_all_product_tags';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'parent_aid, product_type_aid, user_owner_aid, tag';

		$_sql = '
		INSERT INTO view_all_product_tags
		('.$columns.')
		SELECT '.$columns.'
		FROM book_tag 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_tags
		('.$columns.')
		SELECT '.$columns.'
		FROM magazine_tag 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = '
		INSERT INTO view_all_product_tags
		('.$columns.')
		SELECT '.$columns.'
		FROM vdo_tag 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$_sql = 'TRUNCATE TABLE view_all_product_tags_with_detail';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);


		$columns = 'tag, aid, cid, product_type_aid, product_main_aid, title, author, publisher_aid, status, weight, is_new, is_recommended, is_home, publish_date, expired_date, category, upload_path, cover_image_file_type, total_copy, total_view, total_download, total_read, review_point, best_price, has_ebook, has_license, created_date, created_by, updated_date, updated_by';

		$_sql = '
		INSERT INTO view_all_product_tags_with_detail
		('.$columns.')
		SELECT vapt.tag,vap.aid, vap.cid, vap.product_type_aid, vap.product_main_aid, vap.title, vap.author, vap.publisher_aid, vap.status, vap.weight, vap.is_new, vap.is_recommended, vap.is_home, vap.publish_date, vap.expired_date, vap.category, vap.upload_path, vap.cover_image_file_type, vap.total_copy, vap.total_view, vap.total_download, vap.total_read, vap.review_point, vap.best_price, vap.has_ebook, vap.has_license, vap.created_date, vap.created_by, vap.updated_date, vap.updated_by
		FROM view_all_product_tags vapt
		LEFT JOIN view_all_products vap ON vap.aid = vapt.parent_aid AND vap.product_type_aid = vapt.product_type_aid 
		LIMIT 100000;
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);
	
	}

}

/* End of file view_all_products_model.php */
/* Location: ./system/application/model/view_all_products_model.php */