selectValueMapping = {};
inputValueFormatter = $data?.personnel_pivot_personnel_custom_field?.reduce(
    (acc, item) => {
        const field = item?.personnel_custom_field?.field;
        if (field) acc[field] = (val = item?.value) => val ?? null;
        return acc;
    },
    {}
);
