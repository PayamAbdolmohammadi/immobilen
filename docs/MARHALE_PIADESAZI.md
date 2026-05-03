# مراحل پیاده‌سازی به‌ترتیب

این سند ترتیب اجرای کارها را برای محصول HV سبک تعریف می‌کند. هر مرحله را قبل از پرش به بعدی تا «تحویل‌پذیر» جلو ببرید.

**خلاصهٔ همین repo:** فازهای **۰ تا ۶** پیاده شده‌اند؛ **فاز ۷** (UI Stripe/Linear و polish) و **فاز ۸ MVP** (قرارداد PDF، لینک امن مستأجر، `contract_events`، قوانین فاکتور) نیز در کد همین شاخه هستند. **راهنمای دمؤ فروش (WOW)** در **`docs/WOW_DEMO.md`** آمده است. جداول «وضعیت فعلی» زیر هر فاز جزئیات را می‌گویند. فهرست «خارج از MVP» در انتهای سند برای توسعهٔ بعدی است.

---

## فاز صفر — زیرساخت پروژه

1. Laravel، `.env`، اتصال دیتابیس (برای production معمولاً **MySQL/MariaDB** با `utf8mb4`؛ برای تست PHPUnit معمولاً **SQLite `:memory:`** در `phpunit.xml` قابل قبول است).
2. استراتژی چندمستأجری: ستون **`mandant_id`** روی دادهٔ مشتری + تعهد تیمی که **هیچ query بدون فیلتر mandant** از لایهٔ وب بیرون نیاید؛ پیاده‌سازیٔ عملی (middleware، ثبت‌نام + اتصال user به mandant، base controller) اغلب همان **فاز ۱** است، اینجا فقط **قرارداد معماری** را ثبت می‌کنید.
3. قرارداد نام‌گذاری جداول آلمانی (`objekte`, `einheiten`, `mieter`, …) و در صورت نیاز **`protected $table`** روی مدل تا جمع‌کردن انگلیسی Laravel با DB هم‌خوان بماند.

**خروجی:** پروژه بالا می‌آید، مهاجرت‌ها اجرا می‌شوند، تست‌ها سبز، و قانون mandant برای همهٔ مراحل بعد روشن است.

**وضعیت فعلی این repo:** **فاز صفر در کد پوشش داده شد:** Laravel Breeze (Blade)، ثبت‌نام همراه با ایجاد خودکار `mandant` و پر کردن `users.mandant_id`، middleware نام‌دهی‌شده `mandant` برای مسیرهای اپ پس از ورود، مسیر `/mandant/einrichten` برای کاربران legacy بدون مستأجر، `$table` صریح برای جداول آلمانی، و `APP_TIMEZONE` در `.env.example` با خواندن از `config/app.php`. برای assetها در تست/محیط محلی باید یک‌بار `npm run build` اجرا شود (خروجی در `/public/build`؛ این مسیر در `.gitignore` است و در CI باید build شود).

#### چک‌لیست تکمیلی (همان فاز صفر، با جزئیات بیشتر)

- **Laravel و محیط:** PHP و اکستنشن‌ها مطابق `composer.json`؛ `.env.example` → `.env`؛ `APP_KEY`؛ پیشنهاد تایم‌زون پیش‌فرض `Europe/Berlin` برای منطقهٔ هدف. — **DONE**
- **مهاجرت:** `php artisan migrate` بدون خطا؛ در MVP ترجیحاً فقط مهاجرت forward؛ seed دمو اختیاری و جدا از production. — **DONE**
- **Mandant:** بعد از ورود، `mandant_id` کاربر برای همهٔ درخواست‌های دادهٔ مشتری معتبر است (یا سناریوی ثبت‌نام = ایجاد `mandant` + bind به user). — **DONE**
- **کیفیت:** `php artisan test` سبز؛ در این پروژه فرمت با **Pint** پیشنهاد می‌شود. — **DONE**

---

## فاز ۱ — MVP (Cashflow: Rechnung → Bank → Matching → Mahnung)

### ۱.۱ داده و مهاجرت

1. جدول `mandants` و اتصال `users.mandant_id` (در صورت نیاز).
2. `objekte`, `einheiten`, `mieter`.
3. `rechnungen` دستی (`typ = manual`): مبلغ، سررسید، وضعیت، `mandant_id`.
4. (در صورت تعریف در MVP) `bank_imports`, `bank_transactions`, تخصیص پرداخت.

**وضعیت فعلی این repo (هم‌تراز با خطوط بالا):**

| #   | وضعیت                                                                                                                                                                                                                           |
| --- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ۱   | **انجام شده:** `mandants`، `users.mandant_id`، ثبت‌نام + middleware `mandant`.                                                                                                                                                  |
| ۲   | **انجام شده:** مهاجرت و مدل‌های `objekte`، `einheiten`، `mieter` با `mandant_id`.                                                                                                                                               |
| ۳   | **انجام شده:** CRUD فاکتور دستی (`typ = manual`) با لیست/ایجاد/ویرایش/حذف (حذف فقط در وضعیت باز و بدون تخصیص بانک)، محدود به `mandant`.                                                                                         |
| ۴   | **انجام شده:** جداول `bank_imports`، `bank_transactions`، `zahlungszuordnungen`؛ آپلود CSV (ستون‌های تاریخ/مبلغ قابل تشخیص)؛ صفحهٔ بانک با تخصیص دستی (`Zuordnen`) به فاکتور؛ به‌روزرسانی وضعیت فاکتور/تراکنش پس از تخصیص کامل. |

---

### ۱.۲ مدل و منطق

5. مدل‌ها و روابط Eloquent.
6. احراز هویت ساده (ثبت‌نام/ورود) و محدودسازی بر اساس `mandant_id`.
7. سرویس تطبیق بانک: **بدون قطعی‌سازی خودکار**؛ فقط پیشنهاد + تأیید کاربر (`Zuordnen`).

**وضعیت فعلی این repo (بندهای ۱.۲):**

| #   | وضعیت                                                                                                                                                                                          |
| --- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ۵   | **انجام شده:** روابط Eloquent تکمیلی (`Mandant` ↔ `objekte` / `rechnungen` / `users` / بانک؛ `Mieter`/`Einheit` → `rechnungen`؛ `Rechnung` ↔ `BankTransaction` از طریق `zahlungszuordnungen`). |
| ۶   | **انجام شده:** Breeze + middleware `mandant`؛ کوئری‌های دادهٔ مشتری با `mandant_id` و **trait** `ResolvesCurrentMandant` در کنترلرهای بانک/فاکتور دستی (بدون تغییر جریان ثبت‌نام).             |
| ۷   | **انجام شده:** `BankMatchSuggestionService` فقط **پیشنهاد** (مبلغ باز، نام مستأجر/شماره فاکتور در متن بانک)؛ **هیچ ثبت خودکار**؛ قطعی‌سازی تنها با `Zuordnen` و `AllocateBankPayment`.         |

### ۱.۳ UI و دمو

8. داشبورد: فاکتورهای باز، معوق، آخرین پرداخت‌ها.
9. CRUD حداقلی مستأجر / واحد / فاکتور (workflowمحور، نه فرم‌های تزئینی).
10. آپلود CSV بانک، لیست تراکنش‌ها، صفحهٔ تطبیق.
11. Mahnung: ایمیل برای فاکتور معوق (حداقل یک قالب).

**خروجی:** دمو قابل فروش: مستأجر → فاکتور → CSV → match با یک کلیک → وضعیت پرداخت.

**وضعیت فعلی این repo (بندهای ۱.۳):**

| #   | وضعیت                                                                                                                                         |
| --- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| ۸   | **انجام شده:** داشبورد با شمارندهٔ باز/معوق، جدول فاکتورهای معوق (دکمهٔ Mahnung)، پیش‌نمایش باز، آخرین تخصیص‌های بانک؛ لینک‌های workflow.     |
| ۹   | **انجام شده:** CRUD مستأجر (`/mieter`)، ملک (`/objekte`)، واحد (`/einheiten`)؛ فاکتور دستی قبلاً موجود است.                                   |
| ۱۰  | **انجام شده:** آپلود CSV و لیست import در `/bank`؛ صفحهٔ تطبیق اختصاصی `/bank/matching` با Zuordnen؛ پس از import هدایت به matching.          |
| ۱۱  | **انجام شده:** `MahnungMail` + قالب HTML `mail/mahnung`؛ `POST /rechnungen/{rechnung}/mahnung`؛ فقط فاکتور باز + سررسید گذشته + ایمیل مستأجر. |

---

## فاز ۲ — قرارداد و اجارهٔ ماهانه (DONE در repo)

### ۲.۱ پایه (انجام‌شده در کد فعلی)

1. مهاجرت `mietvertraege` + گسترش `rechnungen` (`mietvertrag_id`, `typ`, `billing_period`, `source_data` به **Cent**، `unique(mietvertrag_id, billing_period)` برای MySQL با `manual` و `mietvertrag_id = null`).
2. مدل‌ها و فکتوری‌ها.
3. `App\Domain\Billing\RentAmountCalculator` — خروجی `total_cent` و `source_data` با `*_cent`.
4. `App\Domain\Billing\GenerateMonthlyRentInvoices` — قفل روی ردیف قرارداد، چک فعال بودن/بازه، idempotency.
5. `App\Domain\Leasing\MietvertragService::createLease` — حداکثر یک قرارداد فعال به‌ازای هر `einheit`.
6. تست‌ها: idempotency، دو قرارداد، نادیده گرفتن دورهٔ نامناسب، فاکتور دستی با `null` قرارداد.

### ۲.۲ قدم‌های بعد (به‌ترتیب پیشنهادی)

7. **Artisan command** مثلاً `rents:generate-monthly` که فقط سرویس تولید را صدا بزند (بدون UI).
8. **زمان‌بندی:** `schedule` در production (cron یک خط) + مستند یک‌خطی در README یا همین docs.
9. **UI قرارداد:** لیست / ایجاد / پایان قرارداد (`endContract` از اسکلت به منطق واقعی).
10. **اسکلت‌های باقی‌ماندهٔ سرویس:** `updateContract` (سیاست «فقط آینده»)، `endContract` با تعیین سرنوشت فاکتورهای باز.
11. **داشبورد:** «پرداخت بعدی» / پیش‌نمایش مبلغ (از روی قرارداد، با یادآوری اینکه فاکتور واقعی همان snapshot است).
12. **immutable فاکتور:** ممنوعیت ویرایش هسته روی `rechnungen` از UI؛ مسیر **Storno** برای ابطال فاکتور اجارهٔ باز + امکان صدور مجدد دوره در صورت نیاز.

**وضعیت فعلی این repo (بندهای ۲.۲ / ۷–۱۲):**

| #   | وضعیت                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| --- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ۷   | **انجام شده:** دستور `php artisan rents:generate-monthly` (`--period=`، `--mandant=`) → `GenerateMonthlyRentInvoices`.                                                                                                                                                                                                                                                                                                                                                |
| ۸   | **انجام شده:** در `bootstrap/app.php` زمان‌بندی ماهانه روز ۱ ساعت ۰۶:۰۰؛ در production کرون یک خطی استاندارد Laravel: `* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1`                                                                                                                                                                                                                                                                       |
| ۹   | **انجام شده:** UI `/mietvertraege` (لیست، ایجاد، ویرایش مبالغ، پایان قرارداد `/beenden`).                                                                                                                                                                                                                                                                                                                                                                             |
| ۱۰  | **انجام شده:** `endContract` و `updateContract` واقعی در `MietvertragService`؛ پایان قرارداد فقط وضعیت/تاریخ؛ **فاکتورهای باز اجاره خودکار ابطال نمی‌شوند** (هشدار در UI). به‌روزرسانی مبالغ فقط روی فاکتورهای **آینده** اثر دارد (snapshot روی ردیف `rechnungen`).                                                                                                                                                                                                   |
| ۱۱  | **انجام شده:** بلوک «Active leases — rent preview» روی داشبورد + لینک مدیریت قراردادها.                                                                                                                                                                                                                                                                                                                                                                               |
| ۱۲  | **انجام شده:** `RechnungPolicy::update` برای `typ = rent` همچنان `false`؛ مسیر **`POST /rechnungen/{rechnung}/storno`** + فرم در داشبورد؛ فقط **owner** و فقط در صورت **بدون تخصیص بانک**؛ وضعیت `storniert` + `storniert_am`؛ صدور مجدد همان دوره پس از Storno در `GenerateMonthlyRentInvoices`؛ به‌روزرسانی `next_billing_period` قرارداد برای بازسازی دورهٔ گذشته عقب‌تر کشیده نمی‌شود. ایندکس یکتای قبلی دوره در DB با ایندکس جایگزین (سازگار MySQL) حذف شده است. |

**خروجی:** جملهٔ فروش: _«Die Miete läuft automatisch.»_ — تولید ماهانه پایدار و قابل تکرار.

---

## فاز ۳ — Nebenkosten / Jahresabrechnung (بعد از تثبیت فاز ۲)

1. مدل هزینه‌ها و دورهٔ تسویه (ساده: یک نوع تقسیم).
2. تولید PDF گزارش سالانهٔ ساده.
3. بدون DATEV ورودی در ابتدا؛ فقط در صورت نیاز export بعدی.

**وضعیت فعلی این repo (فاز ۳):**

| #   | وضعیت                                                                                                                                                           |
| --- | --------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ۱   | **انجام شده:** جداول `nk_abrechnungen` + `nk_positionen`؛ تقسیم `gleich_pro_einheit`؛ یک رکورد به ازای `(objekt_id, jahr)`؛ UI ایجاد/لیست/جزئیات + لینک ناوبری. |
| ۲   | **انجام شده:** خروجی PDF با DomPDF (`nk-abrechnungen/{id}/pdf`).                                                                                                |
| ۳   | **طبق طرح:** بدون DATEV در این فاز.                                                                                                                             |

---

## فاز ۴ — پورتال مستأجر

1. ورود جدا برای مستأجر، داده فقط همان واحد/مستأجر.
2. اسناد و فاکتورهای باز؛ بدون تیکت سنگین در شروع.

**وضعیت فعلی این repo (فاز ۴ MVP):**

| #   | وضعیت                                                                                                                                                                                                                               |
| --- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ۱   | **انجام شده:** گارد `mieter` + ستون `password` / `remember_token` روی `mieter`؛ ورود `/portal/login`؛ داشبورد `/portal` فقط دادهٔ همان `mieter_id`؛ رمز پورتال از فرم ویرایش/ایجاد مستأجر (مالک). ایمیل غیرتکراری برای ورود پورتال. |
| ۲   | **انجام شده:** لیست فاکتورهای وضعیت `offen`؛ بلوک «Documents» با متن خالی (بدون آپلود/تیکت).                                                                                                                                        |

---

## فاز ۵ — حسابداری، نقش‌ها، مقیاس

1. خروجی DATEV (یا export ساده برای Steuerberater).
2. نقش‌ها و دسترسی‌ها در سطح عملیاتی.
3. صف‌ها، لاگ خطا برای import بانک، پشتیبانی چند بانک CSV.

**وضعیت فعلی این repo (فاز ۵ MVP):**

| #   | وضعیت                                                                                                                                                                                                                                                                |
| --- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ۱   | **انجام شده:** پایهٔ export حسابداری: مسیر **`GET /export/accounting`** و **`POST /export/accounting/simple-csv`** (فرم: سال + ماه اختیاری؛ UTF‑8 با BOM؛ جداکنندهٔ `;`). DATEV «رسمی/EXTF» نیست. فقط نقش **owner**. (خروجی DATEV-ready در **فاز ۶** تکمیل شده است.) |
| ۲   | **انجام شده:** `users.role`: `owner` / `staff`؛ حذف مستأجر، ملک، واحد فقط **owner**؛ export حسابداری فقط **owner**.                                                                                                                                                  |
| ۳   | **انجام شده:** import بانک از طریق صف (`ProcessBankCsvImportJob`)؛ `bank_imports.csv_profile` و `error_message`؛ ردیف `failed` در UI؛ پروفایل CSV generic / Sparkasse / Comdirect.                                                                                   |

---

## فاز ۶ — Steuerberater / DATEV Export (تکمیل حسابداری) — **DONE**

### ۶.۱ هدف

هدف این فاز ارتقای خروجی حسابداری از یک CSV ساده به یک خروجی قابل استفاده برای Steuerberater است.

تمرکز:

- استانداردسازی خروجی
- سازگاری اولیه با DATEV
- بدون تبدیل پروژه به ERP کامل

**نکته مهم:** این خروجی «DATEV رسمی کامل» نیست، بلکه یک **DATEV-ready Export** برای آماده‌سازی داده‌ها است.

---

### ۶.۲ قدم‌های پیاده‌سازی (به‌ترتیب)

1. **UI Export حسابداری**
    - صفحه `/export/accounting`
    - انتخاب:
        - سال (year)
        - ماه (اختیاری)
        - نوع خروجی:
            - Simple CSV
            - DATEV-ready CSV
        - نوع Kontenrahmen:
            - SKR03
            - SKR04
        - Beraternummer _(فعلاً در UI پیاده نشده — اختیاری برای مرحلهٔ بعد)_
        - Mandantennummer _(همان)_

2. **Route و دسترسی**
    - فقط کاربر با نقش `owner`
    - scope کامل بر اساس `mandant_id`

3. **Controller**
    - `AccountingExportController`
    - متدها:
        - `index`
        - `exportSimpleCsv`
        - `exportDatevCsv`

4. **Service Layer**
    - **`App\Services\Accounting\DatevExportService`** (نام پیشنهادی سند بود `AccountingExportService`؛ در کد همین کلاس استفاده شده است.)
    - مسئول:
        - گرفتن داده از `rechnungen`
        - اتصال به پرداخت‌ها از طریق **`zahlungszuordnungen`** → **`bank_transactions`** (متن بانک در Simple CSV و ستون‌های کمکی)
        - normalize کردن داده
        - ساخت CSV

5. **بهبود Simple CSV**
    - UTF-8 با BOM
    - separator = `;`
    - نام ستون‌ها آلمانی
    - فیلتر year / month
    - فقط داده‌های همان mandant

6. **DATEV-ready Export**
    - ستون‌های پیشنهادی:
        - Umsatz
        - Soll/Haben
        - Konto
        - Gegenkonto
        - Belegdatum
        - Buchungstext
        - Rechnungsnummer
        - Zahlungsstatus

    - mapping ساده:
        - درآمد اجاره:
            - SKR03 → 8400
            - SKR04 → 4400
        - بانک:
            - SKR03 → 1200
            - SKR04 → 1800

    - وضعیت:
        - پرداخت نشده → OFFEN
        - پرداخت شده → BEZAHLT
        - storniert → در **DATEV-ready** export نمی‌آید (جلوگیری از ثبت درآمد مثبت برای Storno)

    - افزوده‌های پیاده‌شده در کد: ستون‌های انتهایی **Netto / Steuer / Brutto** (از `source_data.net_cent` / `steuer_cent` در صورت وجود؛ **Brutto** از `betrag_cent`)؛ متن هشدار UI که این فایل **DATEV-EXTF رسمی** نیست.

7. **قوانین مهم**
    - هیچ داده‌ای خارج از `mandant_id` نباشد
    - هیچ field جدید بدون نیاز واقعی اضافه نشود
    - از schema فعلی استفاده شود

8. **تست‌ها**
    - staff دسترسی نداشته باشد
    - owner دسترسی داشته باشد
    - داده فقط مربوط به mandant باشد
    - فیلتر سال/ماه درست کار کند

9. **Navigation**
    - لینک:
        - "Buchhaltung / Export"
    - فقط برای owner

---

### ۶.۳ خروجی

پس از این فاز، محصول می‌تواند بگوید:

> „Rechnungen, Zahlungen und offene Posten können direkt für den Steuerberater exportiert werden.“

---

### ۶.۴ وضعیت فعلی این repo (فاز ۶):

| بند / موضوع                                                                                     | وضعیت                              |
| ----------------------------------------------------------------------------------------------- | ---------------------------------- |
| UI `/export/accounting`، Simple + DATEV-ready، سال/ماه، SKR03/SKR04                             | **DONE**                           |
| Routeها تحت middleware **`owner`**؛ داده فقط **`mandant_id`** جاری                              | **DONE**                           |
| `AccountingExportController` (`index`, `exportSimpleCsv`, `exportDatevCsv`)                     | **DONE**                           |
| `DatevExportService` — CSV از `rechnungen`، بانک‌متن از `zahlungszuordnungen`                   | **DONE**                           |
| Simple CSV: BOM، `;`، هدر آلمانی، فیلتر سال/ماه                                                 | **DONE**                           |
| DATEV-ready: mapping 8400/4400، 1200/1800، OFFEN/BEZAHLT؛ بدون `storniert`؛ Netto/Steuer/Brutto | **DONE**                           |
| تست‌های feature (دسترسی staff/owner، mandant، فیلتر، SKR03/SKR04)                               | **DONE**                           |
| ناوبری «Buchhaltung / Export» برای owner                                                        | **DONE**                           |
| **Beraternummer / Mandantennummer** در فرم (طبق ۶.۲)                                            | **انجام نشده** — عمدی/اختیاری بعدی |

---

### نتیجه این فاز

- محصول از MVP به **business-ready** نزدیک می‌شود
- امکان استفاده واقعی توسط Steuerberater ایجاد می‌شود
- ارزش فروش پروژه به‌صورت مستقیم افزایش پیدا می‌کند

---

## وضعیت نهایی تحویل در همین repo (MVP + فاز ۶)

| فاز | موضوع                                                                                                                   |  وضعیت   |
| :-: | :---------------------------------------------------------------------------------------------------------------------- | :------: |
|  ۰  | زیرساخت Laravel، `mandant_id`، قرارداد نام‌جداول آلمانی                                                                 | **DONE** |
|  ۱  | Rechnung دستی، بانک CSV، Matching، Mahnung، داشبورد پایه                                                                | **DONE** |
|  ۲  | `mietvertraege`، تولید اجارهٔ ماهانه، UI قرارداد، immutable rent، **Storno**                                            | **DONE** |
|  ۳  | NK جمع هزینه، تقسیم واحدها، PDF سالانه                                                                                  | **DONE** |
|  ۴  | پورتال مستأجر (ورود جدا)، فاکتورهای باز؛ اسناد = placeholder بدون آپلود                                                 | **DONE** |
|  ۵  | پایهٔ export حسابداری، نقش owner/staff، صف import بانک + لاگ خطا + پروفایل CSV                                          | **DONE** |
|  ۶  | DATEV-ready CSV، `AccountingExportController` + `DatevExportService`، `/export/accounting`، SKR03/SKR04، تست‌ها، ناوبری | **DONE** |

**خارج از این بسته (پیشنهاد توسعهٔ بعدی، الزام این سند نیست):** **DATEV EXTF / import کامل** (فایل استاندارد Kanzlei)؛ فیلدهای **Berater-/Mandantennummer** در UI؛ آپلود اسناد در پورتال؛ UI انتساب نقش به کاربر؛ تخصیص بانک جزئی/پیشرفته‌تر؛ بهبودهای UX.

---

## قاعدهٔ طلایی ترتیب

- **اول** جریان پول و اعتماد (فاکتور، بانک، تطبیق، Mahnung).
- **بعد** اتوماسیون اجاره از قرارداد.
- **بعد** NK و پورتال و ادغام‌های سنگین.

اگر یک مرحله هنوز در کد نیست، در PR/شاخه همان مرحله بمان و feature اضافه نکن.

---

## فاز ۸ — Vertrags-PDF + Link an Mieter (**MVP پیاده‌سازی شده**)

**زمینه:** فازهای **۰ تا ۷** تکمیل شده‌اند؛ فاز **۸** از نظر MVP در همین repo پیاده شده است: PDF قرارداد، توکن هَش‌شده با انقضا، مسیر عمومی پذیرش، عملگر در جزئیات قرارداد، جدول **`contract_events`**، و قوانین **`canGenerateInvoices()`** (شامل grandfather برای قراردادهای legacy بدون workflow).

---

### ۱. Goal / Ziel

قرارداد اجاره از «دادهٔ داخلی در CRM» به یک **گردش کار قرارداد رو به مستأجر (customer-facing)** تبدیل می‌شود: PDF رسمی قرارداد، لینک امن، بازبینی و پذیرش/امضاء، سپس فعال‌سازی و آزاد شدن اتوماسیون اجارهٔ ماهانه.

**جملهٔ فروش (آلمانی):**

> «**Vom Vertrag zur automatischen Miete – ohne Papier und ohne manuelle Übergabe.**»

---

### ۲. Scope

**در محدودهٔ MVP همین شاخه (پیاده شده):**

| حوزه      | توضیح کوتاه                                                                                                                                                  |
| :-------- | :----------------------------------------------------------------------------------------------------------------------------------------------------------- |
| PDF       | تولید **Mietvertrag-PDF** با DomPDF؛ ویوی **`contracts.pdf`**؛ ذخیره روی دیسک **`local`** (ریشه: **`storage/app/private`**)؛ مسیر در **`contract_pdf_path`** |
| Link      | توکن تصادفی؛ در DB فقط **SHA-256 hash** (‎`signature_token_hash`‎)؛ انقضا‎ **`token_expires_at`**‎؛ اولین ایجاد لینک می‌تواند‎ **`draft` → `sent`**‎ کند     |
| Tenant UI | مسیرهای عمومی نام‌گذاری‌شده:‎ **`contracts.public.show`**, **`pdf`**, **`accept`**‎؛ throttle روی routeها                                                    |
| Annahme   | UI با تأیید؛ از‎ **`draft`/`sent`**‎ به‎ **`active`**‎ با‎ **`accepted_at`**‎ و‎ **`activated_at`**‎؛ بدون QES                                               |
| Status    | ‎ **`draft`**, **`sent`**, **`active`**, **`ended`**‎؛ قراردادهای قدیمی‎ **`active`**‎ بدون فیلدهای workflow همچنان برای فاکتور **grandfather** می‌شوند      |
| Audit     | جدول **`contract_events`**؛‎ **`actor_type`**:‎ `owner`, `tenant`, `system`                                                                                  |

**صریحاً خارج از محدوده (nicht Teil von Phase 8):**

- Full legal **QES**-Integration
- **Identity verification** (VideoIdent، Ausweisprüfung حرفه‌ای)
- **Complex DMS** (Versionierung enterprise، Workflow حقوقی کامل)
- Full **eIDAS**-Provider-Anbindung
- **WhatsApp** als Versandkanal
- **KI-Vertragserstellung**
- direkte **Payment-API** (Sepa، Kartenzahlung در همین فاز)

---

### ۳. Status model (`mietvertraege`) — پیاده شده در MVP

وضعیت‌های ستون **`status`** در کد فعلی:

| Status   | معنی کوتاه                                                                |
| :------- | :------------------------------------------------------------------------ |
| `draft`  | Entwurf؛ قراردادهای جدید از فرم با این وضعیت ذخیره می‌شوند                |
| `sent`   | لینک/فرآیند به مستأجر آغاز شده (‎`sent_at`‎ می‌تواند پر شود)              |
| `active` | قرارداد عملیاتی؛ مشروط به‎ **`canGenerateInvoices()`**‎ برای فاکتور اجاره |
| `ended`  | پایان قرارداد (مانند قبل)                                                 |

**تفاوت با طرح اولیهٔ سند:** در MVP هیچ ستون‎ **`accepted`**‎ یا‎ **`signed`**‎ به‌عنوان‎ **`status`**‎ وجود ندارد؛ پذیرش مستأجر مستقیماً‎ **`active`**‎ می‌کند و زمان‌ها در‎ **`accepted_at`**‎ و‎ **`activated_at`**‎ ذخیره می‌شوند. وضعیت‎ **`cancelled`**‎ هنوز به‌صورت جداگانه در مدل پیاده نشده است.

**قانون فاکتور:**‎ **`GenerateMonthlyRentInvoices`**‎ از‎ **`Mietvertrag::canGenerateInvoices()`**‎ استفاده می‌کند:‎ **`active`**‎ و یا‎ **`accepted_at`/`activated_at`**‎ یا قرارداد legacy بدون‎ **`sent_at`**‎ و بدون‎ **`signature_token_hash`**‎.

---

### ۴. Datenmodell / Migration (**implementiert**)

**Migration:**‎ `database/migrations/2026_05_07_120000_phase8_contract_pdf_and_events.php`

**ستون‌های اضافه‌شده روی `mietvertraege`:**

| Feld                   | Zweck                                                            |
| :--------------------- | :--------------------------------------------------------------- |
| `sent_at`              | Zeitpunkt Versand / Erstellung Link-Flow                         |
| `accepted_at`          | Tenant hat akzeptiert                                            |
| `activated_at`         | Übergang zu billable automation (auch bei manueller Aktivierung) |
| `contract_pdf_path`    | Relativer Pfad auf Disk `local` (privat)                         |
| `signature_token_hash` | Nur **Hash** (64 Zeichen)، indexiert                             |
| `token_expires_at`     | Ablaufzeit Link                                                  |

**Hinweis:**‎ **`signed_at`**‎ ist **nicht** migriert — später bei echter Signatur/QES.

**Tabelle `contract_events`:**

| Spalte                                            | Implementierung                                                          |
| :------------------------------------------------ | :----------------------------------------------------------------------- |
| `mandant_id`, `mietvertrag_id`                    | FK، index‎ `(mandant_id, mietvertrag_id)`‎                               |
| `event_type`                                      | string‎ `64`‎ (konkrete Eventnamen im Code / Services)                   |
| `actor_type`                                      | ‎ `owner`, `tenant`, `system`‎ (Konstanten im Model **`ContractEvent`**) |
| `actor_id`, `ip_address`, `user_agent`, `payload` | wie geplant                                                              |

**Hinweis:** `contract_events` dient **Nachvollziehbarkeit im Demo** — nicht juristischer Overengineering.

---

### ۵. Workflow

**Owner / HV:**

1. **Mietvertrag** als **draft** anlegen (`MietvertragController@store`).
2. **PDF erzeugen** (`POST …/contract/pdf`, Service **`GenerateContractPdf`**).
3. **Sicheren Link** erzeugen (`POST …/contract/link`)؛ URL wird geflasht؛ Versandkanal liegt beim HV (Copy/Paste).
4. در **`mietvertraege.show`**: Badge، دکمه‌ها، در صورت نیاز **„Vertrag aktivieren“** (‎`POST …/contract/activate`‎).

**Tenant:**

1. **`GET /contracts/{token}`** (Token‎ **48 hex**‎ در‎ `routes/web.php`‎)؛ بدون دسترسی به دادهٔ دیگران؛ بعد از انقضا‎ **`404`**.
2. **`GET /contracts/{token}/pdf`** برای دانلود PDF در صورت وجود فایل.
3. **`POST /contracts/{token}/accept`** با تأیید UI.
4. صفحهٔ تأیید بعد از پذیرش.

**System:**

1. رویدادها در **`contract_events`** (‎`ContractEventService`‎).
2. بعد از پذیرش:‎ **`status = active`**‎،‎ **`accepted_at`**‎،‎ **`activated_at`**.
3. فاکتور اجاره ماهانه فقط برای قراردادهایی که‎ **`canGenerateInvoices()`**‎ برقرار است (+ grandfather).

---

### ۶. UI pages

**Owner (intern):**

- **`mietvertraege.show`**: Status-Badge، **„PDF erzeugen“**، ایجاد/کپی لینک، فعال‌سازی دستی؛ لیست/نمایش رویدادهای قرارداد طبق پیاده‌سازی فعلی.

**Tenant (öffentlich):**

- **`resources/views/contracts/public.blade.php`** (+ **`contracts/pdf.blade.php`** برای PDF): Kurzfassung، لینک دانلود PDF، Checkbox Accept، Hinweistext wie im Template — **kein** auto-generierter Anwaltstext.

---

### ۷. Security (Design rules)

- **Signierte Route** oder **zufälliger Token** in URL؛ **kein Klartext-Token** in DB — nur **Hash**
- **`token_expires_at`**؛ nach Ablauf: neuen Link erzeugen
- Token ist an **`mandant_id` + `mietvertrag_id`** gebunden
- **Kein Zugriff** auf andere Mieterdaten über denselben Mechanismus
- **Rate limiting** auf öffentlicher Route
- **Audit**: IP + User-Agent bei kritischen Events
- **Interne IDs** in öffentlicher URL vermeiden wenn möglich (slug/token nur)

---

### ۸. Automation rule

**Rent invoice generation** (‎`GenerateMonthlyRentInvoices`‎) nutzt **`Mietvertrag::canGenerateInvoices()`**:

- Legacy:‎ **`active`**‎ ohne‎ **`sent_at`**‎ und ohne‎ **`signature_token_hash`**‎ (z.B. WOW-Demo، alte Daten).
- Neu:‎ **`active`**‎ mit‎ **`accepted_at`**‎ oder‎ **`activated_at`**‎ (nach Tenant-Akzeptanz oder manueller Aktivierung).

**Keine** automatischen Rechnungen für‎ **`draft`**‎،‎ **`sent`**‎،‎ **`ended`**‎، oder‎ **`active`**‎ ohne die obigen Zeitstempel/Grandfather-Bedingungen.

---

### ۹. MVP Phase 8 vs. später

| MVP (jetzt im Code)                           | Später                                           |
| :-------------------------------------------- | :----------------------------------------------- |
| PDF erzeugen                                  | Echter Signatur-Anbieter                         |
| Sicherer Link (Hash + Ablauf)                 | **QES / AES / SES** nach Bedarf                  |
| Tenant **Akzeptieren**-Button                 | Identity verification                            |
| Status `draft/sent/active/ended` + Timestamps | Separater `cancelled`-Workflow، ggf. `signed_at` |
| Audit **contract_events**                     | E-Mail-Templates + Versand aus der App           |

---

### ۱۰. Acceptance criteria (MVP — erfüllt)

- Owner legt **Draft-Lease** an.
- Owner erzeugt **PDF**؛ Speicherung unter **`storage/app/private/...`** gemäß **`contract_pdf_path`**.
- Owner erzeugt **sicheren Link**؛ Token nur als Hash in DB.
- Tenant öffnet Link und **akzeptiert**؛ Vertrag wird **active**؛ Timestamps gesetzt.
- **Monatsmiete** nur bei **`canGenerateInvoices()`**؛ Legacy unverändert nutzbar.
- **Multi-tenant:** Route-Model-Binding für‎ **`mietvertrag`**‎ über‎ **`mandant_id`**‎؛ fremde IDs →‎ **404**.
- Feature-Tests:‎ **`tests/Feature/ContractPhase8Test.php`**.

---

### ۱۱. Implementierungsreferenz

Zentrale Einstiegspunkte im Code: **`ContractPublicController`**، **`MietvertragController`** (Contract-Actions)، Services unter **`app/Services/Contract/`**، Policy **`MietvertragPolicy`**، Domain **`MietvertragService`** / **`GenerateMonthlyRentInvoices`**.

---

**Status:** **MVP IMPLEMENTIERT** (ohne QES/externe APIs)

---

_آخرین به‌روزرسانی:_ سند با repo هم‌تراز است؛ **فاز ۰ تا ۶** پیاده و DONE؛ **WOW-Demo** در **`/demo-flow`** و **`docs/WOW_DEMO.md`**. **فاز ۷** در کد انجام شده. **فاز ۸** به‌صورت **MVP در کد پیاده شده** (PDF، لینک امن، جریان عمومی پذیرش، `contract_events`، قوانین فاکتور + grandfather). **خارج از MVP فاز ۸** همچنان طبق بخش «صریحاً خارج از محدوده» بالا (QES، eIDAS-Provider، DMS enterprise، …). تنها باقی‌ماندهٔ صریح فاز ۶ در سند: **Beraternummer / Mandantennummer در فرم** (اختیاری).
