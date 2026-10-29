# fuzette-clean-toc
A lightweight, zero-bloat WordPress plugin that generates a pure and editable HTML Table of Contents

# Fuzette Clean HTML TOC

> **Designed by Argong with Gemini** 

An ultra-lightweight, zero-bloat WordPress plugin that generates a pure, editable HTML Table of Contents (`<ul><li><a href="#fgl1">...</a></li></ul>`) directly at your cursor position inside the editor, automatically injecting clean `id` attributes into your article headings without loading any frontend CSS or JavaScript.

---

## ✨ المميزات الأساسية (Key Features)

1. **كود HTML نقي وقابل للتعديل (100% Editable HTML):**
   يتم توليد الفهرس كقائمة نقطية عادية داخل نص المقالة (`<a href="#fgl1">العنوان الأول</a>`)، مما يتيح لك تعديل النص أو حذف أي عنصر منه بحرية تامة.
2. **صفر عبء على واجهة الموقع (Zero Frontend Overhead):**
   لا تقوم الإضافة بتحميل أي ملفات CSS أو JavaScript للزوار، ولا تستهلك موارد السيرفر عند عرض المقالات.
3. **متوافقة مع جميع المحررات (Universal Editor Support):**
   تعمل بكفاءة مع محرر المكونات (Gutenberg)، والمحرر الكلاسيكي المرئي (TinyMCE)، ومحرر الأكواد النصي (HTML Quicktags).
4. **أمان واستدامة المحتوى (Future-Proof):**
   لأن الروابط والمعرفات (`id="fgl1"`) تُحفظ مباشرة داخل نص المقالة، فستظل تعمل للأبد حتى لو قمت بتعطيل الإضافة أو حذفها لاحقاً.

---

## 🚀 طريقة الاستخدام (How to Use)

1. أثناء كتابة المقالة وتنسيق العناوين (`H2`, `H3`, `H4`)، ضع مؤشر الفأرة في المكان الذي تريد إدراج جدول المحتويات فيه.
2. اضغط على الزر المناسب لمستوى التعمق الذي تريده:
   - **H2:** لإدراج العناوين حتى المستوى الثاني فقط.
   - **H3:** لإدراج العناوين حتى المستوى الثالث.
   - **H4:** لإدراج العناوين حتى المستوى الرابع.
3. ستقوم الإضافة تلقائياً بإضافة `id="fgl1"`, `id="fgl2"` للعناوين وطباعة قائمة الروابط في موضع المؤشر.

### مثال للكود الناتج (Output Example)

```html
<ul>
  <li><a href="#fgl1">العنوان الأول</a></li>
  <li><a href="#fgl2">العنوان الفرعي</a></li>
</ul>

<h2 id="fgl1">العنوان الأول</h2>
<p>محتوى الفقرة...</p>
<h3 id="fgl2">العنوان الفرعي</h3>
```

---

## 📦 التثبيت (Installation)

1. قم بتحميل المستودع بصيغة `ZIP` أو انسخ مجلد `fuzette-clean-toc` إلى المسار `/wp-content/plugins/`.
2. فعّل الإضافة من لوحة تحكم ووردبريس (**إضافات -> إضافات منصبة**).

---

## 📄 الرخصة (License)

مرخصة تحت رخصة المشاع العام **GPLv2 or later** — الكود المصدري متاح للجميع للاستفادة منه وتطويره.
