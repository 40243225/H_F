<script type="text/javascript">
	var XMLWriter = require('xml-writer');
    xw = new XMLWriter;
    xw.startDocument();
    xw.startElement('root');
    xw.writeAttribute('foo', 'value');
    xw.text('Some content');
    xw.endDocument();
　	document.write(xw.toString());
</script>
