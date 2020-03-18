$(() => {
    let clickableRows = $('.js-row-clickable');
    clickableRows.each((index, row) => {
        let $row = $(row);
        let rowUrl = $row.find('[data-js-row-clickable-url]').attr('href');
        $row.on('click', () => {
            window.location.href = rowUrl;
        })
    });
});