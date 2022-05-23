<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:mets="http://www.loc.gov/METS/"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:mods="http://www.loc.gov/mods/v3"
    xmlns:dv="http://dfg-viewer.de/"
    xmlns:xlink="http://www.w3.org/1999/xlink" version="2.0">

    <xsl:output indent="yes"/>

    <xsl:template match="/">

        <xsl:variable name="dmdlog">DMDLOG_0</xsl:variable>
        <xsl:variable name="file">FILE_0</xsl:variable>
        <xsl:variable name="log">LOG_0</xsl:variable>
        <xsl:variable name="phys">PHYS_0</xsl:variable>

        <mets:mets xmlns:mets="http://www.loc.gov/METS/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:mods="http://www.loc.gov/mods/v3"
        xmlns:dv="http://dfg-viewer.de/"
        xmlns:xlink="http://www.w3.org/1999/xlink" xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/mods.xsd http://www.loc.gov/METS/ http://www.loc.gov/standards/mets/mets.xsd">

            <mets:dmdSec ID="DMDLOG_0000">
                <mets:mdWrap MDTYPE="MODS">
                <mets:xmlData>
                    <mods:mods>
                        <mods:titleInfo>
                            <mods:title>3D Objects</mods:title>
                        </mods:titleInfo>
                    </mods:mods>
                </mets:xmlData>
                </mets:mdWrap>
            </mets:dmdSec>

            <xsl:for-each select="response/item">
                <xsl:element name="mets:dmdSec">
                    <xsl:attribute name="ID">
                        <xsl:apply-templates select="@key">
                            <xsl:with-param name="id" select="$dmdlog" tunnel="yes"/>
                        </xsl:apply-templates>
                    </xsl:attribute>
                <mets:mdWrap MDTYPE="MODS">
                    <mets:xmlData>
                        <mods:mods>
                            <mods:titleInfo>
                            <mods:title>
                                <xsl:value-of select="normalize-space(title/text())"/>
                            </mods:title>
                            </mods:titleInfo>
                            <mods:identifier type="gettyaat">
                                <xsl:value-of select="object_type/text()"/>
                            </mods:identifier>
                            <mods:recordInfo>
                                <mods:recordInfoNote>
                                    <xsl:value-of select="description/text()"/>
                                </mods:recordInfoNote>
                        </mods:recordInfo>
                        </mods:mods>
                    </mets:xmlData>
                </mets:mdWrap>
                </xsl:element>
            </xsl:for-each>

        <mets:amdSec ID="AMD">
            <mets:rightsMD ID="RIGHTS">
                <mets:mdWrap MDTYPE="OTHER" MIMETYPE="text/xml" OTHERMDTYPE="DVRIGHTS">
                    <mets:xmlData>
                    <dv:rights>
                        <dv:owner>3D Repository</dv:owner>
                        <dv:ownerLogo>http://dfg-viewer.de/fileadmin/_processed_/a/8/csm_HSM_Logo_T_schwarz_klein_bold_regular_d61a371993.jpg</dv:ownerLogo>
                        <dv:ownerSiteURL>https://3d-repository.hs-mainz.de/</dv:ownerSiteURL>
                        <dv:ownerContact>https://3d-repository.hs-mainz.de/contact</dv:ownerContact>
                    </dv:rights>
                    </mets:xmlData>
                </mets:mdWrap>
            </mets:rightsMD>
            <mets:digiprovMD ID="DIGIPROV">
                <mets:mdWrap MDTYPE="OTHER" MIMETYPE="text/xml" OTHERMDTYPE="DVLINKS">
                    <mets:xmlData>
                    <dv:links>
                        <!-- this link cases problem: _format -->
                        <!-- anyway this value is going to be filled by value from source XML -->
                        <dv:reference>https://3d-repository.hs-mainz.de/export_xml_multi</dv:reference>
                        <dv:presentation>https://3d-repository.hs-mainz.de/</dv:presentation>
                    </dv:links>
                    </mets:xmlData>
                </mets:mdWrap>
            </mets:digiprovMD>
        </mets:amdSec>

        <mets:fileSec>
            <mets:fileGrp USE="THUMBS">
                <xsl:for-each select="response/item">
                    <mets:file MIMETYPE="image/jpeg">
                        <xsl:attribute name="ID">
                            <xsl:apply-templates select="@key">
                                <xsl:with-param name="id" select="$file" tunnel="yes"/>
                            </xsl:apply-templates>
                        </xsl:attribute>
                        <xsl:apply-templates select="preview_image/text()" />
                    </mets:file>
                </xsl:for-each>
            </mets:fileGrp>
        </mets:fileSec>
        <mets:structMap TYPE="LOGICAL">
            <mets:div ADMID="AMD" DMDID="DMDLOG_0000" ID="LOG_0000" LABEL="3D Objects" TYPE="collection">
                <xsl:for-each select="response/item">
                    <mets:div TYPE="object">
                        <xsl:attribute name="CONTENTIDS">
                            <xsl:value-of select="normalize-space(single_export/text())" disable-output-escaping="yes"/>
                        </xsl:attribute>
                        <xsl:attribute name="DMDID">
                            <xsl:apply-templates select="@key">
                                <xsl:with-param name="id" select="$dmdlog" tunnel="yes"/>
                            </xsl:apply-templates>
                        </xsl:attribute>
                        <xsl:attribute name="ID">
                            <xsl:apply-templates select="@key">
                                <xsl:with-param name="id" select="$log" tunnel="yes"/>
                            </xsl:apply-templates>
                        </xsl:attribute>
                        <xsl:attribute name="LABEL">
                            <xsl:value-of select="normalize-space(title/text())"/>
                        </xsl:attribute>
                        <mets:mptr LOCTYPE="URL">
                            <xsl:attribute name="xlink:href">
                                <xsl:text>http://sdvtypo3dfgviewer3ddev.slub-dresden.de/typo3conf/ext/dfgviewer/Resources/Public/single_object_</xsl:text><xsl:value-of select="@key"/><xsl:text>.xml</xsl:text>
                            </xsl:attribute>
                        </mets:mptr>
                    </mets:div>
                </xsl:for-each>
            </mets:div>
        </mets:structMap>

        <mets:structMap TYPE="PHYSICAL">
            <mets:div ID="PHYS_0000" LABEL="3D Objects" TYPE="collection">
                <xsl:for-each select="response/item">
                    <mets:div ORDERLABEL="1" TYPE="object">
                        <xsl:attribute name="ID">
                            <xsl:apply-templates select="@key">
                                <xsl:with-param name="id" select="$phys" tunnel="yes"/>
                            </xsl:apply-templates>
                        </xsl:attribute>
                        <xsl:attribute name="ORDER">
                            <xsl:value-of select="number(@key) + 1"/>
                        </xsl:attribute>
                        <xsl:attribute name="ORDERLABEL">
                            <xsl:value-of select="number(@key) + 1"/>
                        </xsl:attribute>
                            <mets:fptr>
                                <xsl:attribute name="FILEID">
                                    <xsl:apply-templates select="@key">
                                        <xsl:with-param name="id" select="$file" tunnel="yes"/>
                                    </xsl:apply-templates>
                                </xsl:attribute>
                            </mets:fptr>
                    </mets:div>
                </xsl:for-each>
            </mets:div>
        </mets:structMap>

        <mets:structLink>
            <xsl:for-each select="response/item">
                <mets:smLink>
                    <xsl:attribute name="xlink:to">
                        <xsl:apply-templates select="@key">
                            <xsl:with-param name="id" select="$phys" tunnel="yes"/>
                        </xsl:apply-templates>
                    </xsl:attribute>
                    <xsl:attribute name="xlink:from">
                        <xsl:apply-templates select="@key">
                            <xsl:with-param name="id" select="$log" tunnel="yes"/>
                        </xsl:apply-templates>
                    </xsl:attribute>
                </mets:smLink>
            </xsl:for-each>
        </mets:structLink>

        </mets:mets>

    </xsl:template>

    <xsl:template match="@key">
        <xsl:param name="id" tunnel="yes"/>
        <xsl:param name="key" select="."/>
        <xsl:choose>
            <xsl:when test="not(number($key) > 8)">
                <xsl:value-of select="concat($id, '00', number($key) + 1)"/>
            </xsl:when>
            <xsl:when test="not(number($key) > 98)">
                <xsl:value-of select="concat($id, '0', number($key) + 1)"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="concat($id, number($key) + 1)"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="preview_image/text()">
        <xsl:variable select="tokenize(.,'&#x22;')" name="previewImage" />

        <mets:FLocat LOCTYPE="URL">
            <xsl:attribute name="xlink:href">
                <xsl:value-of select="$previewImage[2]"/>
            </xsl:attribute>
        </mets:FLocat>
    </xsl:template>

</xsl:transform>