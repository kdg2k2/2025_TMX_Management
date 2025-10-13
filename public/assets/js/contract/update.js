selectValueMapping = {
    'many_years[]': (item) => item.year,
    'scopes[]': (item) => item.province_code,
    'instructors[]': (item) => item.user_id,
    'professionals[]': (item) => item.user_id,
    'disbursements[]': (item) => item.user_id,
    'intermediate_collaborators[]': (item) => item.user_id,
    'contract_status': (obj) => obj.original,
    'intermediate_product_status': (obj) => obj.original,
    'financial_status': (obj) => obj.original,
};

inputValueFormatter = {
    'signed_date': (value) => formatDateToYmd(value),
    'effective_date': (value) => formatDateToYmd(value),
    'end_date': (value) => formatDateToYmd(value),
    'acceptance_date': (value) => formatDateToYmd(value),
    'liquidation_date': (value) => formatDateToYmd(value),
    'completion_date': (value) => formatDateToYmd(value),
};

const afterAutoMatchFieldAndFillPatchFormDone = () => {
    refreshSubmitFrom();
};
