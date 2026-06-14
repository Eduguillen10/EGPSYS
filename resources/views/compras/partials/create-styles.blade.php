<style>
    .tm-create-header,
    .presupuesto-header,
    .orden-header,
    .compra-create-header {
        margin-bottom: 18px;
    }

    .tm-create-panel,
    .pedido-panel,
    .presupuesto-panel,
    .orden-panel,
    .compra-create-panel,
    .remision-panel,
    .ajuste-panel {
        background: var(--tm-surface, #fff);
        border: 1px solid var(--tm-border, #d9e2ec);
        border-radius: 4px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .06);
        margin-bottom: 16px;
        padding: 16px;
    }

    .tm-create-action,
    .presupuesto-action,
    .orden-action,
    .compra-create-action,
    .remision-action {
        background: #f8fcff;
        border: 1px solid #b7d8ee;
        border-left: 4px solid var(--tm-primary, #3f8fc9);
        border-radius: 4px;
        padding: 16px;
    }

    .tm-create-action h4,
    .presupuesto-action h4,
    .orden-action h4,
    .compra-create-action h4,
    .remision-action h4,
    .remision-panel-title {
        color: var(--tm-text, #2c3e50);
        font-weight: 700;
        margin-top: 0;
    }

    .tm-detail-panel,
    .ajuste-detail-panel,
    .panel.panel-primary {
        border-color: #b7d8ee;
        border-radius: 4px;
        overflow: hidden;
    }

    .tm-detail-heading,
    .ajuste-detail-heading,
    .panel.panel-primary > .panel-heading {
        background: var(--tm-sidebar, #2f3a4a);
        border-color: var(--tm-sidebar, #2f3a4a);
        color: #fff;
        font-weight: 700;
    }

    .tm-detail-body,
    .ajuste-detail-body,
    .panel.panel-primary > .panel-body {
        background: #fff;
    }

    table.table thead,
    table.table thead[style] {
        background-color: var(--tm-table-head, #ffe08a) !important;
        color: var(--tm-text, #2f3a45);
    }

    .btn-info,
    .btn-primary {
        background-color: var(--tm-primary, #3f8fc9);
        border-color: var(--tm-primary-hover, #347fab);
        color: #fff;
    }

    .btn-info:hover,
    .btn-info:focus,
    .btn-primary:hover,
    .btn-primary:focus {
        background-color: var(--tm-primary-hover, #347fab);
        border-color: #2d6f96;
        color: #fff;
    }

    .btn-success {
        background-color: var(--tm-success, #3fa56b);
        border-color: var(--tm-success-hover, #348b5a);
    }

    .btn-success:hover,
    .btn-success:focus {
        background-color: var(--tm-success-hover, #348b5a);
        border-color: #2c754c;
    }

    .btn-danger {
        background-color: var(--tm-danger, #d9534f);
        border-color: var(--tm-danger-hover, #c94a46);
    }

    .form-control[readonly],
    .form-control[disabled] {
        background-color: #f5f7f9;
        color: #4b5563;
    }
</style>
