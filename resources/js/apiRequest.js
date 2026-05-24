const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

export async function callApi(path, options = {}) {
    const hasBody = options.body !== undefined;

    const response = await fetch(path, {
        credentials: "same-origin",
        ...options,
        headers: {
            Accept: "application/json",
            ...(hasBody ? { "Content-Type": "application/json" } : {}),
            ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {}),
            ...(options.headers || {}),
        },
        body: hasBody ? JSON.stringify(options.body) : undefined,
    });

    const data = await response.json().catch(() => null);

    if (!response.ok) {
        const validationMessage = data?.errors
            ? Object.values(data.errors).flat().join("\n")
            : null;

        throw new Error(
            validationMessage || data?.message || `HTTP ${response.status}`,
        );
    }

    return data;
}
