<!-- partial:query -->
<div id="query" ga-body-templates title="Query templates">
    <div class="col-md-4">
        <select class="form-control" ga-term="object:8321" ga-selectedIndex="0" onchange=selectChange(this)>
            <option label="ilike" value="string:ilike" selected>ilike</option>
            <option label="not ilike" value="string:not ilike">not ilike</option>
        </select>
    </div>
    <div class="col-md-4">
        <select class="form-control" ga-term="object:8322" ga-selectedIndex="0" onchange=selectChange(this)>
            <option label="=" value="string:=" selected>=</option>
            <option label="!=" value="string:!=">!=</option>
            <option label=">" value="string:>">&gt;</option>
            <option label="<" value="string:<">&lt;</option>
            <option label="<=" value="string:<=">&lt;=</option>
            <option label=">=" value="string:>=">&gt;=</option>
        </select>
    </div>
    <div class="col-md-12">
        <input type="text"class="form-control" style="border-radius: 4px" ga-term="object:8321"
            title="epalinges, ependes (vd), grub (ar), leuk, uesslingen-buch ..."
            placeholder="epalinges, ependes (vd), grub  ...">
    </div>
    <div class="col-md-12">
        <input type="text"class="form-control" style="border-radius: 4px" ga-term="object:8322"
            title="3031, 4616, 5584, 5914, 6110 ..."
            placeholder="3031, 4616, 5584, 5914, 6110 ...">
    </div>
</div>