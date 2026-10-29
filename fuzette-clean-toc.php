<?php
/**
 * Plugin Name: Fuzette Clean HTML TOC
 * Plugin URI: https://fuzette.com
 * Description: أداة خفيفة جداً لتوليد جدول محتويات كـ HTML نقي وقابل للتعديل مباشرة في موضع المؤشر مع إضافة ID تلقائي للعناوين، بدون أي CSS خارجي. مصممة بواسطة Argong بمساعدة Gemini.
 * Version: 1.0.0
 * Author: Argong (with Gemini)
 * Author URI: https://fuzette.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fuzette-clean-toc
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Fuzette_Clean_HTML_TOC {

    public function __construct() {
        // إدراج السكريبت في صفحات إضافة وتعديل المقالات فقط
        add_action( 'admin_footer-post.php', array( $this, 'inject_editor_script' ) );
        add_action( 'admin_footer-post-new.php', array( $this, 'inject_editor_script' ) );
        
        // إضافة أزرار للمحرر الكلاسيكي والـ Quicktags
        add_action( 'media_buttons', array( $this, 'add_classic_editor_buttons' ), 15 );
    }

    /**
     * أزرار تظهر فوق المحرر الكلاسيكي مباشرة
     */
    public function add_classic_editor_buttons() {
        echo '<span style="margin-right:10px; display:inline-block;">';
        echo '<button type="button" class="button" onclick="fuzGenerateCleanTOC(2)">+ فهرس (حتى H2)</button> ';
        echo '<button type="button" class="button" onclick="fuzGenerateCleanTOC(3)">+ فهرس (حتى H3)</button> ';
        echo '<button type="button" class="button" onclick="fuzGenerateCleanTOC(4)">+ فهرس (حتى H4)</button>';
        echo '</span>';
    }

    /**
     * السكريبت المسؤول عن فحص العناوين، إضافة الـ IDs، وطباعة الـ HTML النظيف مكان المؤشر
     */
    public function inject_editor_script() {
        ?>
        <script type="text/javascript">
        (function() {
            // إضافة الأزرار في محرر النص (HTML Quicktags)
            if (typeof QTags !== 'undefined') {
                QTags.addButton('fuz_clean_toc_2', 'فهرس H2', function() { fuzGenerateCleanTOC(2); });
                QTags.addButton('fuz_clean_toc_3', 'فهرس H3', function() { fuzGenerateCleanTOC(3); });
                QTags.addButton('fuz_clean_toc_4', 'فهرس H4', function() { fuzGenerateCleanTOC(4); });
            }

            // إضافة شريط أزرار عائم صغير في حال استخدام محرر المكونات (Gutenberg)
            window.addEventListener('load', function() {
                if (window.wp && wp.data && wp.data.select('core/block-editor')) {
                    var toolbar = document.createElement('div');
                    toolbar.style.cssText = 'position:fixed; bottom:20px; left:20px; z-index:99999; background:#fff; padding:8px 12px; border:1px solid #ccd0d4; border-radius:4px; box-shadow:0 2px 6px rgba(0,0,0,0.15); direction:rtl; font-family:sans-serif;';
                    toolbar.innerHTML = '<strong style="margin-left:8px; font-size:12px;">إدراج فهرس نصي:</strong>' +
                        '<button type="button" class="button button-small" style="margin-left:4px;" onclick="fuzGenerateCleanTOC(2)">H2</button>' +
                        '<button type="button" class="button button-small" style="margin-left:4px;" onclick="fuzGenerateCleanTOC(3)">H3</button>' +
                        '<button type="button" class="button button-small" style="margin-left:4px;" onclick="fuzGenerateCleanTOC(4)">H4</button>';
                    document.body.appendChild(toolbar);
                }
            });

            // الدالة الأساسية لتوليد الكود النظيف
            window.fuzGenerateCleanTOC = function(maxLevel) {
                var counter = 1;

                // دالة مساعدة لمعالجة كود HTML وإضافة المعرفات وبناء القائمة
                function parseAndInjectIds(htmlContent) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(htmlContent, 'text/html');
                    
                    // تحديد المستويات المطلوبة (من h1 حتى المستوى الذي اختاره المستخدم)
                    var selectorList = [];
                    for (var i = 1; i <= maxLevel; i++) {
                        selectorList.push('h' + i);
                    }
                    
                    var headings = doc.querySelectorAll(selectorList.join(','));
                    if (headings.length === 0) {
                        return null;
                    }

                    var tocItems = [];
                    headings.forEach(function(heading) {
                        var text = heading.textContent.trim();
                        if (!text) return;

                        // إذا لم يكن للعنوان id مسبق، ننشئ له id قصير ونظيف مثل fgl1, fgl2
                        var id = heading.getAttribute('id');
                        if (!id) {
                            id = 'fgl' + counter;
                            heading.setAttribute('id', id);
                        }
                        counter++;

                        tocItems.push('<li><a href="#' + id + '">' + text + '</a></li>');
                    });

                    var cleanTocHtml = '<ul>\n' + tocItems.join('\n') + '\n</ul>';
                    return {
                        toc: cleanTocHtml,
                        updatedContent: doc.body.innerHTML
                    };
                }

                // 1. التعامل مع محرر المكونات (Gutenberg)
                if (window.wp && wp.data && wp.data.select('core/block-editor')) {
                    var blockEditor = wp.data.select('core/block-editor');
                    var blockDispatch = wp.data.dispatch('core/block-editor');
                    var blocks = blockEditor.getBlocks();
                    var tocLinks = [];

                    blocks.forEach(function(block) {
                        if (block.name === 'core/heading') {
                            var level = block.attributes.level || 2;
                            if (level <= maxLevel) {
                                var tempDiv = document.createElement('div');
                                tempDiv.innerHTML = block.attributes.content || '';
                                var cleanText = tempDiv.textContent.trim();

                                if (cleanText) {
                                    var anchor = block.attributes.anchor;
                                    if (!anchor) {
                                        anchor = 'fgl' + counter;
                                        blockDispatch.updateBlockAttributes(block.clientId, { anchor: anchor });
                                    }
                                    counter++;
                                    tocLinks.push('<li><a href="#' + anchor + '">' + cleanText + '</a></li>');
                                }
                            }
                        }
                    });

                    if (tocLinks.length === 0) {
                        alert('لم يتم العثور على عناوين في المقالة حتى المستوى H' + maxLevel);
                        return;
                    }

                    var listBlock = wp.blocks.createBlock('core/list', {
                        values: tocLinks.join('')
                    });

                    // إدراج القائمة في موضع المؤشر الحالي داخل المحرر
                    var selectedBlock = blockEditor.getSelectedBlock();
                    if (selectedBlock) {
                        var index = blockEditor.getBlockIndex(selectedBlock.clientId);
                        blockDispatch.insertBlocks(listBlock, index);
                    } else {
                        blockDispatch.insertBlocks(listBlock, 0);
                    }
                    return;
                }

                // 2. التعامل مع المحرر الكلاسيكي المرئي (TinyMCE Visual Editor)
                if (typeof tinymce !== 'undefined' && tinymce.activeEditor && !tinymce.activeEditor.isHidden()) {
                    var editor = tinymce.activeEditor;
                    // حفظ موضع المؤشر الحالي
                    var bookmark = editor.selection.getBookmark(2, true);
                    var content = editor.getContent();
                    var result = parseAndInjectIds(content);

                    if (!result) {
                        alert('لم يتم العثور على عناوين في المقالة حتى المستوى H' + maxLevel);
                        return;
                    }

                    // تحديث العناوين بالـ IDs ثم إعادة المؤشر لمكانه وإدراج الروابط
                    editor.setContent(result.updatedContent);
                    editor.selection.moveToBookmark(bookmark);
                    editor.insertContent(result.toc);
                    return;
                }

                // 3. التعامل مع محرر الأكواد النصي (Plain Textarea)
                var textarea = document.getElementById('content');
                if (textarea) {
                    var cursorPos = textarea.selectionStart;
                    var marker = '___FUZ_CURSOR_MARKER___';
                    var textWithMarker = textarea.value.substring(0, cursorPos) + marker + textarea.value.substring(textarea.selectionEnd);

                    var result = parseAndInjectIds(textWithMarker);
                    if (!result) {
                        alert('لم يتم العثور على عناوين في المقالة حتى المستوى H' + maxLevel);
                        return;
                    }

                    textarea.value = result.updatedContent.replace(marker, result.toc + '\n');
                }
            };
        })();
        </script>
        <?php
    }
}

new Fuzette_Clean_HTML_TOC();
