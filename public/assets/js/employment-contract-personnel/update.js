selectValueMapping = {};
inputValueFormatter = $data?.employment_contract_personnel_pivot_employment_contract_personnel_custom_field?.reduce(
    (acc, item) => {
        const field = item?.employment_contract_personnel_custom_field?.field;
        if (field) acc[field] = (val = item?.value) => val ?? null;
        return acc;
    },
    {}
);
