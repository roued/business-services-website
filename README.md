# 🏢 منصة خدمات الأعمال المتكاملة | Business Services Platform

## 📋 وصف المشروع

منصة شاملة وموثوقة تقدم مجموعة متنوعة من الخدمات المهنية والإدارية لرجال الأعمال والشركات في المملكة العربية السعودية.

## 🎯 الخدمات المقدمة

### 1. **الأنظمة الإدارية (ERP Systems)**
- تطبيق أنظمة ERP متطورة
- إدارة الموارد البشرية
- إدارة المخزون والمبيعات
- التقارير والتحليلات

### 2. **تصميم الجرافيك**
- تصميم الشعارات
- تصميم المطبوعات
- تصميم واجهات المستخدم
- الرسوميات والصور

### 3. **التسويق الإلكتروني**
- إدارة وسائل التواصل الاجتماعي
- حملات إعلانية رقمية
- تحسين محركات البحث (SEO)
- تسويق المحتوى

### 4. **الخدمات المحاسبية**
- الدفاتر المحاسبية
- الفواتير والتقارير المالية
- استشارات ضريبية
- تدقيق الحسابات

### 5. **الخدمات القانونية**
- تأسيس الشركات
- الاستشارات القانونية
- التأمينات والعقود
- الامتثال القانوني

### 6. **السياحة والسفر**
- حزم سياحية شاملة
- تنظيم الرحلات البحرية
- حجوزات الفنادق والتذاكر
- خدمات الاستقبال والتوصيل

### 7. **تأجير السيارات**
- سيارات اقتصادية وفاخرة
- حجوزات سهلة وسريعة
- خدمات 24/7
- تأمين شامل

### 8. **مراكز الصيانة**
- صيانة دورية
- إصلاح الأعطال
- الخدمات الطارئة
- استشارات فنية

## 🛠️ المتطلبات التقنية

### الجانب الخادم (Backend)
- **PHP 7.4+** أو أحدث
- **MySQL 5.7+** أو **MariaDB 10.3+**
- **Apache** مع تفعيل mod_rewrite
- **HTTPS** للأمان

### الجانب العميل (Frontend)
- **HTML5**
- **CSS3** مع دعم Grid و Flexbox
- **JavaScript (ES6+)**
- **Responsive Design** لجميع الأجهزة

## 📁 هيكل المشروع

```
business-services-website/
├── config/                 # ملفات الإعدادات
│   ├── database.php       # إعدادات قاعدة البيانات
│   ├── session.php        # إعدادات الجلسات
│   └── functions.php      # الدوال العامة
├── api/                    # واجهات API
│   ├── auth.php           # المصادقة والتسجيل
│   ├── services.php       # الخدمات
│   ├── orders.php         # الطلبات
│   ├── car_rental.php     # تأجير السيارات
│   ├── maintenance.php    # الصيانة
│   └── contact.php        # نموذج الاتصال
├── admin/                  # لوحة التحكم
│   ├── dashboard.html
│   ├── users.html
│   └── ...
├── css/                    # ملفات التنسيقات
│   ├── style.css          # التنسيقات الرئيسية
│   └── responsive.css     # التصميم المستجيب
├── js/                     # ملفات JavaScript
│   ├── main.js            # الدوال الأساسية
│   ├── auth.js            # منطق المصادقة
│   ├── services.js        # منطق الخدمات
│   ├── car-rental.js      # منطق تأجير السيارات
│   ├── maintenance.js     # منطق الصيانة
│   └── contact.js         # منطق الاتصال
├── images/                 # الصور والأيقونات
├── index.html             # الصفحة الرئيسية
├── services.html          # صفحة الخدمات
├── car-rental.html        # صفحة تأجير السيارات
├── maintenance.html       # صفحة الصيانة
├── contact.html           # صفحة الاتصال
├── login.html             # صفحة الدخول
├── register.html          # صفحة التسجيل
└── database.sql           # نص إنشاء قاعدة البيانات
```

## 🚀 خطوات التثبيت

### 1. تحضير البيئة
```bash
# استنساخ المستودع
git clone https://github.com/roued/business-services-website.git
cd business-services-website
```

### 2. إعداد قاعدة البيانات
```bash
# تسجيل الدخول إلى MySQL
mysql -u root -p

# تشغيل ملف إنشاء قاعدة البيانات
source database.sql;
```

### 3. تعديل إعدادات الاتصال

عدّل ملف `config/database.php`:
```php
define('DB_HOST', 'localhost');      // عنوان الخادم
define('DB_USER', 'root');            // اسم المستخدم
define('DB_PASSWORD', 'password');    // كلمة المرور
define('DB_NAME', 'business_services');
```

### 4. رفع الملفات
- انسخ جميع ملفات المشروع إلى جذر الويب (public_html أو www)
- تأكد من إنشاء مجلد `uploads` للصور
- تأكد من صلاحيات الكتابة للمجلدات

### 5. اختبار التثبيت
```
افتح المتصفح وانتقل إلى: http://localhost/business-services-website/
```

## 🔐 الميزات الأمنية

✅ **تشفير كلمات المرور**
- استخدام bcrypt للتشفير الآمن

✅ **حماية من SQL Injection**
- استخدام Prepared Statements

✅ **التحقق من صحة المدخلات**
- Sanitization و Validation

✅ **CSRF Token Protection**
- حماية من طلبات مزيفة

✅ **HTTPS Support**
- دعم الاتصالات الآمنة

✅ **Session Management**
- إدارة آمنة للجلسات

## 📊 قاعدة البيانات

### الجداول الرئيسية:

1. **users** - المستخدمين والعملاء
2. **service_categories** - فئات الخدمات
3. **services** - الخدمات المقدمة
4. **orders** - الطلبات والفواتير
5. **payments** - المدفوعات
6. **car_rentals** - السيارات المتاحة
7. **car_bookings** - حجوزات السيارات
8. **maintenance_centers** - مراكز الصيانة
9. **maintenance_appointments** - مواعيد الصيانة
10. **reviews** - التقييمات والتعليقات

## 🎨 التصميم والتجربة

### المميزات:
✨ تصميم حديث واحترافي
✨ واجهة سهلة الاستخدام (UI/UX)
✨ دعم كامل للعربية والإنجليزية
✨ تصميم مستجيب (Responsive) لجميع الأجهزة
✨ تحميل سريع وأداء عالي
✨ الوصول بسهولة

## 🔄 واجهات API

### المصادقة
```
POST /api/auth.php?action=login
POST /api/auth.php?action=register
POST /api/auth.php?action=logout
```

### الخدمات
```
GET /api/services.php?action=get_categories
GET /api/services.php?action=get_by_category&category_id=1
GET /api/services.php?action=get_service&service_id=1
GET /api/services.php?action=search&query=keyword
```

### الطلبات
```
GET /api/orders.php?action=list
GET /api/orders.php?action=get&order_id=1
POST /api/orders.php?action=create
```

### السيارات
```
GET /api/car_rental.php?action=get_cars
GET /api/car_rental.php?action=get_car&car_id=1
POST /api/car_rental.php?action=book
```

### الصيانة
```
GET /api/maintenance.php?action=get_centers
GET /api/maintenance.php?action=get_by_city&city=Riyadh
POST /api/maintenance.php?action=book_appointment
```

## 📱 دعم الأجهزة

- ✅ أجهزة سطح المكتب
- ✅ الأجهزة اللوحية
- ✅ الهواتف الذكية
- ✅ جميع متصفحات الويب الحديثة

## 🤝 المساهمة

نرحب بمساهماتك! يرجى:

1. عمل Fork للمستودع
2. إنشاء فرع جديد (`git checkout -b feature/AmazingFeature`)
3. Commit التغييرات (`git commit -m 'Add some AmazingFeature'`)
4. Push إلى الفرع (`git push origin feature/AmazingFeature`)
5. فتح Pull Request

## 📝 الترخيص

هذا المشروع مرخص تحت MIT License

## 📞 التواصل والدعم

### معلومات الاتصال:
- **الهاتف:** +966 1 234 5678
- **البريد الإلكتروني:** info@business-services.com
- **الموقع:** www.business-services.com
- **المدينة:** الرياض، المملكة العربية السعودية

### ساعات العمل:
- الأحد - الخميس: 8:00 ص - 9:00 م
- الجمعة والسبت: مغلق

---

**تم إنشاء هذا المشروع بواسطة:** roued

**آخر تحديث:** 2026-05-08

**الإصدار:** 1.0.0