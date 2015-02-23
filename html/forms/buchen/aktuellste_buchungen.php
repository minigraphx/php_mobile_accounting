<div data-role="page" id="aktuellste_buchungen">
  <div data-role="header" data-position="fixed">
    <a href="#buchen_menue" data-role="button" data-icon="home">Zur&uuml;ck</a>
    <h1 data-bind="text:i4l.general.buchhaltung"></h1>
  </div><!-- /header -->
  <div data-role="content">
    <h4>Aktuellste Buchungen</h4>
    <table data-bind="foreach: buchen().buchungen">
      <tr>
        <td data-bind="text:buchungsnummer"></td>
        <td data-bind="text:buchungstext" colspan="3"></td>
      </tr>
      <tr>
        <td data-bind="text:sollkonto"></td>
        <td data-bind="text:habenkonto"></td>
        <td data-bind="text:betrag" class="td_betrag"></td>
        <td data-bind="text:datum_de"></td>
      </tr>
    </table>
  </div><!-- /content -->
  <div data-role="footer" data-position="fixed">
    <h4 data-bind="text:i4l.general.copy"></h4>
  </div><!-- /footer --> 
</div><!-- /page -->
