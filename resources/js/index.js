import {
    ClassicEditor,
    AccessibilityHelp,
    Alignment,
    Autoformat,
    AutoImage,
    AutoLink,
    Autosave,
    BlockQuote,
    Bold,
    Code,
    CodeBlock,
    Essentials,
    FindAndReplace,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    GeneralHtmlSupport,
    Heading,
    Highlight,
    HorizontalLine,
    HtmlComment,
    HtmlEmbed,
    ImageBlock,
    ImageCaption,
    ImageInline,
    ImageInsert,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Indent,
    IndentBlock,
    Italic,
    Link,
    LinkImage,
    List,
    ListProperties,
    MediaEmbed,
    PageBreak,
    Paragraph,
    PasteFromOffice,
    RemoveFormat,
    SelectAll,
    ShowBlocks,
    SimpleUploadAdapter,
    SourceEditing,
    SpecialCharacters,
    SpecialCharactersArrows,
    SpecialCharactersCurrency,
    SpecialCharactersEssentials,
    SpecialCharactersLatin,
    SpecialCharactersMathematical,
    SpecialCharactersText,
    Strikethrough,
    Style,
    Subscript,
    Superscript,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TableToolbar,
    TextTransformation,
    TodoList,
    Underline,
    Undo
} from 'ckeditor5';

// Enable all of these to be accessible from the window object
window.ClassicEditor = ClassicEditor;
window.AccessibilityHelp = AccessibilityHelp;
window.Alignment = Alignment;
window.Autoformat = Autoformat;
window.AutoImage = AutoImage;
window.AutoLink = AutoLink;
window.Autosave = Autosave;
window.BlockQuote = BlockQuote;
window.Bold = Bold;
window.Code = Code;
window.CodeBlock = CodeBlock;
window.Essentials = Essentials;
window.FindAndReplace = FindAndReplace;
window.FontBackgroundColor = FontBackgroundColor;
window.FontColor = FontColor;
window.FontFamily = FontFamily;
window.FontSize = FontSize;
window.GeneralHtmlSupport = GeneralHtmlSupport;
window.Heading = Heading;
window.Highlight = Highlight;
window.HorizontalLine = HorizontalLine;
window.HtmlComment = HtmlComment;
window.HtmlEmbed = HtmlEmbed;
window.ImageBlock = ImageBlock;
window.ImageCaption = ImageCaption;
window.ImageInline = ImageInline;
window.ImageInsert = ImageInsert;
window.ImageInsertViaUrl = ImageInsertViaUrl;
window.ImageResize = ImageResize;
window.ImageStyle = ImageStyle;
window.ImageTextAlternative = ImageTextAlternative;
window.ImageToolbar = ImageToolbar;
window.ImageUpload = ImageUpload;
window.Indent = Indent;
window.IndentBlock = IndentBlock;
window.Italic = Italic;
window.Link = Link;
window.LinkImage = LinkImage;
window.List = List;
window.ListProperties = ListProperties;
window.MediaEmbed = MediaEmbed;
window.PageBreak = PageBreak;
window.Paragraph = Paragraph;
window.PasteFromOffice = PasteFromOffice;
window.RemoveFormat = RemoveFormat;
window.SelectAll = SelectAll;
window.ShowBlocks = ShowBlocks;
window.SimpleUploadAdapter = SimpleUploadAdapter;
window.SourceEditing = SourceEditing;
window.SpecialCharacters = SpecialCharacters;
window.SpecialCharactersArrows = SpecialCharactersArrows;
window.SpecialCharactersCurrency = SpecialCharactersCurrency;
window.SpecialCharactersEssentials = SpecialCharactersEssentials;
window.SpecialCharactersLatin = SpecialCharactersLatin;
window.SpecialCharactersMathematical = SpecialCharactersMathematical;
window.SpecialCharactersText = SpecialCharactersText;
window.Strikethrough = Strikethrough;
window.Style = Style;
window.Subscript = Subscript;
window.Superscript = Superscript;
window.Table = Table;
window.TableCaption = TableCaption;
window.TableCellProperties = TableCellProperties;
window.TableColumnResize = TableColumnResize;
window.TableProperties = TableProperties;
window.TableToolbar = TableToolbar;
window.TextTransformation = TextTransformation;
window.TodoList = TodoList;
window.Underline = Underline;
window.Undo = Undo;

function allowNestedTables ( editor ) {
    editor.model.schema.on('checkChild', (evt, args) => {
        const context = args[0];
        const childDefinition = args[1];
        if (context.endsWith('tableCell') && childDefinition && childDefinition.name == 'table') {
            // Prevent next listeners from being called.
            evt.stop();
            // Set the checkChild()'s return value.
            evt.return = true;
        }
    }, {
        priority: 'highest'
    });
}

ClassicEditor.defaultConfig = {
    extraPlugins: [allowNestedTables],
};

window.ClassicEditor = ClassicEditor;

import 'ckeditor5/ckeditor5.css';
