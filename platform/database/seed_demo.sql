PRAGMA foreign_keys = ON;

INSERT OR IGNORE INTO users (id,name,email,role,active) VALUES
(1,'مدیر نمونه','admin@example.test','admin',1),
(2,'نمونه مشتری بغداد','buyer@example.test','customer',1),
(3,'نمونه مشتری دبی','trade@example.test','customer',1),
(4,'نمونه مشتری استانبول','store@example.test','customer',1),
(5,'نمونه مشتری دوحه','doha@example.test','customer',1),
(6,'نمونه مشتری برلین','berlin@example.test','customer',1),
(7,'نمونه مشتری مسقط','muscat@example.test','customer',1);

INSERT OR IGNORE INTO customers (id,user_id,country_code,country_name,whatsapp,company_name,direct_link_enabled) VALUES
(1,2,'IQ','Iraq','+964 7XX XXX XXXX','نمونه تجاری بغداد',1),
(2,3,'AE','United Arab Emirates','+971 5X XXX XXXX','نمونه تجاری دبی',1),
(3,4,'TR','Türkiye','+90 5XX XXX XXXX','نمونه تجاری استانبول',1),
(4,5,'QA','Qatar','+974 3XXX XXXX','نمونه تجاری دوحه',1),
(5,6,'DE','Germany','+49 15X XXXXXXX','نمونه تجاری برلین',1),
(6,7,'OM','Oman','+968 9XXX XXXX','نمونه تجاری مسقط',1);

INSERT OR IGNORE INTO categories (id,code,name_fa,name_ar,name_en,name_ku) VALUES
(1,'1','نوزادی','حديثو الولادة','Baby','ساوا'),
(2,'2','بچگانه','أطفال','Kids','منداڵان'),
(3,'3','زنانه','نسائي','Women','ژنان');

INSERT OR IGNORE INTO subcategories (id,category_id,code,name_fa,name_ar,name_en,name_ku) VALUES
(11,1,'11','لباس نوزادی','ملابس حديثي الولادة','Baby clothing','جل و بەرگی ساوا'),
(12,1,'12','پتوی نوزادی','بطانية أطفال','Baby blanket','پەتووی ساوا'),
(21,2,'21','دخترانه','بناتي','Girls','کچان'),
(22,2,'22','پسرانه','ولادي','Boys','کوڕان'),
(31,3,'31','زیرسارافون (تونیک)','تونيك','Tunic','تونیک'),
(32,3,'32','لباس راحتی (ورزشی)','ملابس مريحة','Casual sportswear','جلی ئاسوودە');

INSERT OR IGNORE INTO products (id,subcategory_id,code,name_fa,availability,sort_order) VALUES
(1,11,'11001','ست نوزادی آرام','available',10),
(2,11,'11002','بادی نوزادی پایه','available',20),
(3,11,'11003','ست بیمارستانی نوزاد','made_to_order',30),
(4,12,'12001','پتوی نوزادی نرم','available',40),
(5,12,'12002','پتوی دورپیچ نوزاد','available',50),
(6,12,'12003','پتوی سبک چهارفصل','made_to_order',60),
(7,21,'21001','ست دخترانه روزمره','available',70),
(8,21,'21002','تونیک دخترانه','made_to_order',80),
(9,21,'21003','ست راحتی دخترانه','available',90),
(10,22,'22001','ست پسرانه شهری','unavailable',100),
(11,22,'22002','تی‌شرت و شلوارک پسرانه','available',110),
(12,22,'22003','ست راحتی پسرانه','made_to_order',120),
(13,31,'31001','تونیک زنانه مودست','available',130),
(14,31,'31002','زیرسارافون مینیمال','made_to_order',140),
(15,31,'31003','تونیک روزمره بلند','available',150),
(16,32,'32001','ست راحتی زنانه','available',160),
(17,32,'32002','ست ورزشی مودست','made_to_order',170),
(18,32,'32003','بلوز و شلوار راحتی','available',180);

INSERT OR IGNORE INTO product_images (product_id,path,alt_fa,sort_order)
SELECT id,
  CASE subcategory_id
    WHEN 11 THEN 'media/web-stock/sub-11.webp'
    WHEN 12 THEN 'media/web-stock/sub-12.webp'
    WHEN 21 THEN 'media/web-stock/sub-21.webp'
    WHEN 22 THEN 'media/web-stock/sub-22.webp'
    WHEN 31 THEN 'media/web-stock/sub-31.webp'
    WHEN 32 THEN 'media/web-stock/sub-32.webp'
  END,
  name_fa,
  0
FROM products
WHERE id BETWEEN 1 AND 18
AND NOT EXISTS (SELECT 1 FROM product_images pi WHERE pi.product_id=products.id AND pi.sort_order=0);

INSERT OR IGNORE INTO favorites (customer_id,product_id) VALUES
(1,1),(1,4),(1,13),(2,7),(2,11),(3,16),(4,2),(4,15),(5,9),(6,18);

INSERT OR IGNORE INTO orders (id,customer_id,public_code,request_type,status,prepayment_status,notes) VALUES
(1,1,'AR-DEMO-1042','available','production','confirmed','داده نمایشی'),
(2,2,'AR-DEMO-1043','brand','inquiry','not_required','داده نمایشی'),
(3,3,'AR-DEMO-1044','custom','awaiting_prepayment','pending','داده نمایشی'),
(4,4,'AR-DEMO-1045','packaging','ready_to_ship','confirmed','داده نمایشی');

INSERT OR IGNORE INTO order_items (id,order_id,product_id,product_code_snapshot,product_name_snapshot,quantity,negotiated_specs_json) VALUES
(1,1,1,'11001','ست نوزادی آرام',120,'{"size":"negotiable"}'),
(2,1,4,'12001','پتوی نوزادی نرم',80,'{"material":"negotiable"}'),
(3,2,13,'31001','تونیک زنانه مودست',150,'{"brand":"customer"}'),
(4,3,16,'32001','ست راحتی زنانه',100,'{"fit":"negotiable"}'),
(5,4,7,'21001','ست دخترانه روزمره',200,'{"packaging":"customer"}');

INSERT OR IGNORE INTO order_timeline (id,order_id,event_key,title,detail,created_by_user_id) VALUES
(1,1,'inquiry','ثبت استعلام','نمونه نمایشی',1),
(2,1,'specs','تأیید مشخصات','نمونه نمایشی',1),
(3,1,'prepayment','تأیید پیش‌پرداخت','نمونه نمایشی',1),
(4,1,'production','در حال تولید','نمونه نمایشی',1),
(5,3,'inquiry','ثبت درخواست تولید سفارشی','نمونه نمایشی',1),
(6,3,'prepayment','در انتظار پیش‌پرداخت','نمونه نمایشی',1);

INSERT OR IGNORE INTO activity_log (id,actor_user_id,customer_id,action,subject_type,subject_id,metadata_json) VALUES
(1,1,1,'demo_customer_review','customer',1,'{"demo":true}'),
(2,1,3,'demo_order_status_update','order',3,'{"demo":true}');
