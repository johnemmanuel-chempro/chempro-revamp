export default function SiteFooter() {
  return (
    <footer className="bg-light border-top mt-auto py-4">
      <div className="container">
        <div className="row">
          <div className="col-md-6">
            <p className="mb-0 text-muted small">
              &copy; {new Date().getFullYear()} Chempro. All rights reserved.
            </p>
          </div>
          <div className="col-md-6 text-md-end">
            <p className="mb-0 text-muted small">Powered by Next.js + Laravel API</p>
          </div>
        </div>
      </div>
    </footer>
  );
}
