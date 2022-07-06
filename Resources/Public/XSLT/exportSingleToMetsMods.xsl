<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:mets="http://www.loc.gov/METS/"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:mods="http://www.loc.gov/mods/v3"
    xmlns:dv="http://dfg-viewer.de/"
    xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0">

    <xsl:output indent="yes"/>

    <xsl:template match="/">

        <mets:mets xmlns:mets="http://www.loc.gov/METS/"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:mods="http://www.loc.gov/mods/v3"
            xmlns:dv="http://dfg-viewer.de/"
            xmlns:xlink="http://www.w3.org/1999/xlink" xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/mods.xsd http://www.loc.gov/METS/ http://www.loc.gov/standards/mets/mets.xsd">

            <xsl:apply-templates select="response/item" />

        </mets:mets>

    </xsl:template>

    <xsl:template match = "response/item">

        <mets:dmdSec>
            <xsl:attribute name="ID">
                <xsl:choose>
                    <xsl:when test="not(number(@key) > 9)">
                        <xsl:value-of select="concat('DMDLOG_000', number(@key) + 1)"/>
                    </xsl:when>
                    <xsl:when test="not(number(@key) > 99)">
                        <xsl:value-of select="concat('DMDLOG_00', number(@key) + 1)"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="concat('DMDLOG_0', number(@key) + 1)"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <mets:mdWrap MDTYPE="MODS">
                <mets:xmlData>
                    <mods:mods>
                        <mods:titleInfo>
                            <mods:title>
                                <xsl:value-of select="title/text()"/>
                            </mods:title>
                        </mods:titleInfo>
                        <mods:name type="personal">
                            <mods:nameIdentifier type="orcid" typeURI="http://id.loc.gov/vocabulary/identifiers/orcid">
                                <xsl:value-of select="author_orcid/text()"/>                            
                            </mods:nameIdentifier>
                            <mods:namePart type="family">
                            </mods:namePart>
                            <mods:namePart type="given">
                            </mods:namePart>
                            <mods:displayForm>
                                <xsl:value-of select="author_name/text()"/>
                            </mods:displayForm>
                            <mods:role>
                                <mods:roleTerm authority="marcrelator" type="code">aut</mods:roleTerm>
                            </mods:role>
                        </mods:name>
                        <!-- probably type will need to be value taken from source file -->
                        <mods:name type="corporate">
                            <mods:nameIdentifier type="viaf" typeURI="http://id.loc.gov/vocabulary/identifiers/viaf">
                                <xsl:value-of select="holder_viaf/text()"/> 
                            </mods:nameIdentifier>
                            <mods:namePart type="family">
                            </mods:namePart>
                            <mods:namePart type="given">
                            </mods:namePart>
                            <mods:displayForm>
                                <xsl:value-of select="holder_name/text()"/>
                            </mods:displayForm>
                            <mods:role>
                                <mods:roleTerm authority="marcrelator" type="code">prv</mods:roleTerm>
                            </mods:role>
                        </mods:name>
                        <mods:originInfo eventType="digitisation">
                            <mods:publisher>
                                <xsl:value-of select="model_publisher/text()"/>
                            </mods:publisher>
                            <mods:dateCreated point="start" encoding="iso8601">
                                <xsl:value-of select="publication_period_start/text()"/>
                            </mods:dateCreated>
                            <mods:dateCreated point="end" encoding="iso8601">
                                <xsl:value-of select="publication_period_end/text()"/>
                            </mods:dateCreated>
                            <mods:dateIssued encoding="iso8601">
                                <xsl:value-of select="publication_date/text()"/>
                            </mods:dateIssued>
                        </mods:originInfo>
                        <mods:relatedItem type="original">
                            <mods:titleInfo>
                                <mods:title>
                                    <xsl:value-of select="object_name/text()"/>
                                </mods:title>
                            </mods:titleInfo>
                            <mods:titleInfo type="alternative">
                                <mods:title>
                                    <xsl:value-of select="object_alternative_name/text()"/>
                                </mods:title>
                            </mods:titleInfo>
                            <!-- probably type will need to be value taken from source file -->
                            <mods:name type="personal">
                                <!-- inserted conditionally depending on existence in source file  -->
                                <mods:nameIdentifier type="orcid" typeURI="http://id.loc.gov/vocabulary/identifiers/orcid">
                                </mods:nameIdentifier>
                                <!-- inserted conditionally depending on existence in source file  -->
                                <mods:namePart type="family">
                                </mods:namePart>
                                <!-- inserted conditionally depending on existence in source file  -->
                                <mods:namePart type="given">
                                </mods:namePart>
                                <mods:displayForm>
                                </mods:displayForm>
                                <mods:role>
                                    <mods:roleTerm authority="marcrelator" type="code">cre</mods:roleTerm>
                                </mods:role>
                            </mods:name>
                            <mods:originInfo>
                                <mods:dateCreated point="start" encoding="iso8601">
                                    <xsl:value-of select="reconstructed_period_start/text()"/>
                                </mods:dateCreated>
                                <mods:dateCreated point="end" encoding="iso8601">
                                    <xsl:value-of select="reconstructed_period_end/text()"/>
                                </mods:dateCreated>
                                <mods:dateOther encoding="iso8601" type="reconstruction">
                                </mods:dateOther>
                            </mods:originInfo>
                            <mods:location>
                                <mods:physicalLocation valueURI="">
                                    <xsl:value-of select="city/text()"/>
                                </mods:physicalLocation>
                                <mods:url displayLabel="geonames">
                                    <xsl:value-of select="geonames/text()"/>
                                </mods:url>
                                <mods:url displayLabel="wikidata">
                                    <xsl:value-of select="wikidata/text()"/>
                                </mods:url>
                                <mods:url displayLabel="wikipedia">
                                    <xsl:value-of select="wikipedia/text()"/>
                                </mods:url>
                            </mods:location>
                        </mods:relatedItem>
                        <mods:identifier type="gettyaat">
                            <xsl:value-of select="object_type/text()"/>
                        </mods:identifier>
                        <mods:accessCondition type="license">
                            <xsl:value-of select="license/text()"/>
                        </mods:accessCondition>
                        <mods:recordInfo>
                            <mods:recordIdentifier source="[Unique Repository Identifier]">
                            </mods:recordIdentifier>
                            <mods:recordInfoNote>
                                <xsl:value-of select="model_description/text()"/>
                            </mods:recordInfoNote>
                        </mods:recordInfo>
                    </mods:mods>
                </mets:xmlData>
            </mets:mdWrap>
        </mets:dmdSec>
        <mets:amdSec ID="AMD">
            <mets:rightsMD ID="RIGHTS">
                <mets:mdWrap MDTYP="OTHER" MIMETYPE="text/xml" OTHERMDTYPE="DVRIGHTS">
                    <mets:xmlData>
                        <dv:rights>
                            <!-- Name of Repository or Provider -->
                            <dv:owner>3D Repository</dv:owner>
                            <!-- URL: Repository or Provider Logo -->
                            <dv:ownerLogo>http://dfg-viewer.de/fileadmin/_processed_/a/8/csm_HSM_Logo_T_schwarz_klein_bold_regular_d61a371993.jpg</dv:ownerLogo>
                            <!-- URL: Homepage of Repository or Provider -->
                            <dv:ownerSiteURL>https://3d-repository.hs-mainz.de/</dv:ownerSiteURL>
                            <!-- URL: Link to Contact Form or mailto-Link for Contact -->
                            <dv:ownerContact>https://3d-repository.hs-mainz.de/contact</dv:ownerContact>
                        </dv:rights>
                    </mets:xmlData>
                </mets:mdWrap>
            </mets:rightsMD>
            <mets:digiprovMD ID="DIGIPROV">
                <mets:mdWrap MDTYPE="OTHER" MIMETYPE="text/xml" OTHERMDTYPE="DVLINKS">
                    <mets:xmlData>
                        <dv:links>
                            <dv:reference><!-- URL: Link to this object's entry in repository --></dv:reference>
                            <dv:presentation><!-- URL: (Perma-)Link to this object's default presentation --></dv:presentation>
                        </dv:links>
                    </mets:xmlData>
                </mets:mdWrap>
            </mets:digiprovMD>
        </mets:amdSec>
        <mets:fileSec>
            <mets:fileGrp USE="DEFAULT">
                <mets:file ID="FILE_0001_DEFAULT" MIMETYPE="model/gltf-binary">
                    <mets:FLocat LOCTYPE="URL">
                        <xsl:attribute name="xlink:href">
                            <xsl:value-of select="converted_file/text()"/>
                        </xsl:attribute>
                    </mets:FLocat>
                </mets:file>
            </mets:fileGrp>
            <mets:fileGrp USE="THUMBS">
                <mets:file ID="FILE_0001_THUMBS" MIMETYPE="image/jpeg">
                    <xsl:apply-templates select="preview_image/text()" />
                </mets:file>
            </mets:fileGrp>
        </mets:fileSec>
        <mets:structMap TYPE="LOGICAL">
            <!-- can be there other TYPE than object? -->
            <mets:div ID="LOG_0001" TYPE="object" DMDID="DMDLOG_0001">
                <xsl:attribute name="LABEL">
                    <xsl:value-of select="title/text()"/>
                </xsl:attribute>
            </mets:div>
        </mets:structMap>
        <mets:structMap TYPE="PHYSICAL">
            <mets:div ID="PHYS_0000" TYPE="physSequence">
                <mets:div ID="PHYS_0001" ORDER="1" ORDERLABEL="1" TYPE="object">
                    <mets:fptr FILEID="FILE_0001_DEFAULT"/>
                    <mets:fptr FILEID="FILE_0001_THUMBS"/>
                </mets:div>
            </mets:div>
        </mets:structMap>
        <mets:structLink>
            <mets:smLink xlink:to="PHYS_0001" xlink:from="LOG_0001"/>
        </mets:structLink>

    </xsl:template>

    <xsl:template match="preview_image/text()">
        <xsl:variable select="substring-after(.,'&#x22;')" name="previewImage" />

        <xsl:if test="$previewImage != ''">
            <xsl:variable select="substring-before($previewImage,'&quot;')" name="url" />
            <mets:FLocat LOCTYPE="URL">
                <xsl:attribute name="xlink:href">
                    <xsl:value-of select="normalize-space($url)"/>
                </xsl:attribute>
            </mets:FLocat>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>