function mudarAba(tipo) {
    const url = new URL(window.location.href);
    url.searchParams.set('tipo', tipo);
    window.location.href = url.toString();
}