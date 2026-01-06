const vehicleId = document.getElementById('vehicle-id');
const currentKmInput = document.querySelector('[name="current_km"]');

vehicleId.addEventListener('change', ({ target }) => {
    const option = target.selectedOptions[0];
    const vehicle = option?.dataset?.record && JSON.parse(option.dataset.record);

    if (!vehicle) return;

    currentKmInput.value = vehicle.current_km ?? '';

    if (vehicle.current_km) {
        alertInfo('Chỉ điều chỉnh số km hiện trạng khi: thực tế số km trên xe khác số km hệ thống');
    }
});
