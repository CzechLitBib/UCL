<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:marc="http://www.loc.gov/MARC21/slim"
  version="1.0">

  <xsl:output method="xml" indent="yes" encoding="UTF-8"/>
  <xsl:strip-space elements="*"/>

  <xsl:template match="marc:datafield[@tag=910]"/>

  <xsl:template match="@* | node()">
    <xsl:copy>
      <xsl:apply-templates select="@* | node()"/>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="marc:controlfield[@tag!=001]"/>

  <xsl:template match="marc:datafield[not(contains('046 100 368 370 372 400 678', @tag))]"/>

</xsl:stylesheet>
