<div {{sed_attrs}} class="s-tb-sm s-tb-sm module module-table table-skin2 {{class}}">
    <div class="table-responsive">
        <table class="table table-hover {{#ifCond table_bordered "===" true }} table-bordered {{/ifCond}} {{#ifCond table_striped "===" true }} table-striped {{/ifCond}}">{{{content}}}</table>
    </div>
</div>