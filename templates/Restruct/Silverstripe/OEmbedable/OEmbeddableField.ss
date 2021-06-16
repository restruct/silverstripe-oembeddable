<% if $isReadonly %>
	<span id="$ID"
	      <% if $extraClass %>class="$extraClass"<% end_if %>
	      <% if $Description %>title="$Description"<% end_if %>>
		$Value
	</span>
<% else %>
	<div class="embeddableUrl input-group">
		$SourceURL.addExtraClass('form-control').Field
		<div class="input-group-append">
            <input class="btn btn-outline-dark loadEmbeddableData" type="button"
                value="<% _t('OEmbeddable.INSPECT','Inspect') %>"
                data-href="$Link(update)"
                data-trnsl-inspect="<% _t('OEmbeddable.INSPECT','Inspect') %>"
                data-trnsl-loading="<% _t('OEmbeddable.LOADING','Loading') %>"
                />
        </div>
	</div>

    <% if $ObjectTitle %>
    <div class="embeddableProperties">

        <div class="embeddableThumb mb-2">
        $ThumbImage
        </div>

        <div class="form-row mb-2">
            <div class="col">
                <% with $ObjectTitle %><label class="fieldholder-small-label" for="$ID">$Title</label>$Field<% end_with %>
            </div>
        </div>
        <div class="form-row mb-2">
            <div class="col">
                <% with $ObjectDescription.setRows(2) %><label class="fieldholder-small-label" for="$ID">$Title</label>$Field<% end_with %>
            </div>
        </div>
        <div class="form-row">
            <div class="col-xs-6 col-md-3">
                <% with $Width %><label class="fieldholder-small-label" for="$ID">$Title</label>$Field<% end_with %>
            </div>
            <div class="col-xs-6 col-md-3">
                <% with $Height %><label class="fieldholder-small-label" for="$ID">$Title</label>$Field<% end_with %>
            </div>
            <div class="col-md-6">
                <% with $ExtraClass %><label class="fieldholder-small-label" for="$ID">$Title</label>$Field<% end_with %>
            </div>
        </div>
        <%-- some hidden fields that also need to get posted --%>
        $ThumbURL.Field
        $Type.Field
        $EmbedHTML.Field <%-- this one may be visible if 'editableEmbedCode' is true, probably needs some additional styling in that case --%>
    </div>
	<% end_if %>
<% end_if %>
