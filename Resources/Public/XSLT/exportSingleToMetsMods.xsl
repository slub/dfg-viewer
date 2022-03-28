<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:mets="http://www.loc.gov/METS/"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:mods="http://www.loc.gov/mods/v3"
    xmlns:dv="http://dfg-viewer.de/"
    xmlns:xlink="http://www.w3.org/1999/xlink" version="2.0">

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
                                <xsl:choose>
                                    <xsl:when test="generated_title/text()">
                                        <xsl:value-of select="generated_title/text()"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="manually_entered_title/text()"/>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </mods:title>
                        </mods:titleInfo>
                        <mods:name type="personal">
                            <!-- inserted conditionally depending on existence in source file  -->
                            <mods:nameIdentifier type="orcid" typeURI="http://id.loc.gov/vocabulary/identifiers/orcid">
                                <xsl:value-of select="author_orcid/text()"/>                            
                            </mods:nameIdentifier>
                            <!-- inserted conditionally depending on existence in source file  -->
                            <mods:namePart type="family">
                            </mods:namePart>
                            <!-- inserted conditionally depending on existence in source file  -->
                            <mods:namePart type="given">
                            </mods:namePart>
                            <mods:displayForm>
                                <xsl:value-of select="author/text()"/>
                            </mods:displayForm>
                            <mods:role>
                                <mods:roleTerm authority="marcrelator" type="code">aut</mods:roleTerm>
                            </mods:role>
                        </mods:name>
                        <!-- probably type will need to be value taken from source file -->
                        <mods:name type="corporate">
                            <!-- inserted conditionally depending on existence in source file  -->
                            <mods:nameIdentifier type="gnd" typeURI="http://id.loc.gov/vocabulary/identifiers/gnd">
                            </mods:nameIdentifier>
                            <!-- inserted conditionally depending on existence in source file  -->
                            <mods:namePart type="family">
                            </mods:namePart>
                            <!-- inserted conditionally depending on existence in source file  -->
                            <mods:namePart type="given">
                            </mods:namePart>
                            <mods:displayForm>
                                <xsl:value-of select="holder/text()"/>
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
                            </mods:dateCreated>
                            <mods:dateCreated point="end" encoding="iso8601">
                            </mods:dateCreated>
                            <mods:dateIssued encoding="iso8601">
                                <xsl:value-of select="publication_date/text()"/>
                            </mods:dateIssued>
                        </mods:originInfo>
                        <mods:note type="publication status">
                            <xsl:value-of select="publishing_status/text()"/>
                        </mods:note>
                        <mods:relatedItem type="original">
                            <mods:titleInfo>
                                <mods:title>
                                    <xsl:value-of select="object_title/text()"/>
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
                                    <xsl:value-of select="time_showed_on_the_model/text()"/>
                                </mods:dateCreated>
                                <mods:dateCreated point="end" encoding="iso8601">
                                    <xsl:value-of select="time_showed_on_the_model/text()"/>
                                </mods:dateCreated>
                                <mods:dateOther encoding="iso8601" type="reconstruction">
                                </mods:dateOther>
                            </mods:originInfo>
                            <mods:location>
                                <mods:physicalLocation valueURI="">
                                    <xsl:value-of select="object_location/text()"/>
                                </mods:physicalLocation>
                            </mods:location>
                        </mods:relatedItem>
                        <!-- inserted conditionally depending on existence in source file  -->
                        <mods:identifier type="">
                        </mods:identifier>
                        <mods:accessCondition type="license">
                            <xsl:value-of select="license/text()"/>
                        </mods:accessCondition>
                        <mods:recordInfo>
                            <mods:recordIdentifier source="[Unique Repository Identifier]">
                            </mods:recordIdentifier>
                            <mods:recordInfoNote>
                                <xsl:value-of select="description/text()"/>
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
                            <dv:ownerLogo>https://3d-repository.hs-mainz.de/logo.png</dv:ownerLogo>
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
                <xsl:apply-templates select="upload/text()" />
            </mets:fileGrp>
            <mets:fileGrp USE="THUMBS">
                <mets:file ID="FILE_0001_THUMBS" MIMETYPE="image/jpeg">
                    <xsl:apply-templates select="images_upload/text()" />
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

    <xsl:template match="upload/text()">
        <xsl:variable select="tokenize(.,'&#x22;')" name="upload" />

        <mets:file ID="FILE_0001_DEFAULT">
            <xsl:attribute name="MIMETYPE">
                <xsl:value-of select="$upload[6]"/>
            </xsl:attribute>
            <mets:FLocat LOCTYPE="URL">
                <xsl:attribute name="xlink:href">
                    <xsl:text>https://3d-repository.hs-mainz.de</xsl:text><xsl:value-of select="$upload[4]"/>
                </xsl:attribute>
            </mets:FLocat>
        </mets:file>
    </xsl:template>

    <xsl:template match="images_upload/text()">
        <xsl:variable select="tokenize(.,'&#x22;')" name="previewImage" />

        <mets:FLocat LOCTYPE="URL">
            <xsl:attribute name="xlink:href">
                <xsl:text>https://3d-repository.hs-mainz.de</xsl:text><xsl:value-of select="$previewImage[2]"/>
            </xsl:attribute>
        </mets:FLocat>
    </xsl:template>

</xsl:transform>