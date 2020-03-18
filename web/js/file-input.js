$(() => {
    let fileInputContainers = $('.file-input-container');
    fileInputContainers.each((index, fileInputContainer) => {
        let container = $(fileInputContainer);
        let input = container.find('.file-input');
        let label = container.find('.file-input-label');
        let filenameSpan = container.find('.file-input-filename');

        input.on('change', (e) => {
            let splittedFakePath = e.target.value.split('\\');
            filenameSpan.text(splittedFakePath[splittedFakePath.length-1]);
        });

        input.on('dragenter', (e) => {
            label.addClass('dragging');
        });

        input.on('dragleave', (e) => {
            label.removeClass('dragging');
        });
    });
});