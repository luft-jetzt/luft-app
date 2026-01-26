import 'datatables.net-bs5';

$(document).ready(function() {
    $('.datatable').DataTable({
        'paging':   false,
        'info':     false,
        'language': {
            'search': 'Suche:'
        }
    });
});
