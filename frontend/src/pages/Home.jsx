import React, { useState, useEffect } from "react";
import axios from "axios";
import "bootstrap/dist/css/bootstrap.min.css";
import LeadTable from "../components/LeadTable";
import { getReports, saveReport, deleteReport } from "../services/api.js";
import { exportReportExcel, exportReportPdf } from "../services/api.js";

const Home = () => {
  const [availableFields, setAvailableFields] = useState([]);
  const [criteria, setCriteria] = useState([]);
  const [fields, setFields] = useState([]);
  const [reportName, setReportName] = useState("");
  const [reports, setReports] = useState([]);
  const [selectedReports, setSelectedReports] = useState([]);
  const [reportData, setReportData] = useState(null);
  const [file, setFile] = useState(null);

  useEffect(() => {
    // Fetch available fields from backend
    axios.get("http://127.0.0.1:8000/api/report-fields").then((res) => {
      setAvailableFields(res.data);
    });

    // Fetch saved reports
    fetchReports();
  }, []);

  const fetchReports = () => {
    getReports().then((res) => {
      setReports(res.data);
    });
  };

  const handleCriteriaChange = (field) => {
    setCriteria((prev) =>
      prev.includes(field) ? prev.filter((c) => c !== field) : [...prev, field]
    );
  };

  const handleFieldToggle = (field) => {
    setFields((prev) =>
      prev.includes(field) ? prev.filter((f) => f !== field) : [...prev, field]
    );
  };

  const saveReportTemplate = () => {
    if (!reportName || criteria.length === 0 || fields.length === 0) {
      alert("Nama report, kriteria, dan field harus diisi");
      return;
    }
    saveReport({ name: reportName, criteria, fields }).then(() => {
      setReportName("");
      setCriteria([]);
      setFields([]);
      fetchReports();
    });
  };

  const handleDeleteReport = (id) => {
    deleteReport(id).then(() => {
      fetchReports();
    });
  };


  const handleExport = async (id, format) => {
    try {
      let response;
      if (format === 'excel') {
        response = await exportReportExcel(id);
      } else if (format === 'pdf') {
        response = await exportReportPdf(id);
      } else {
        alert('Format export tidak didukung');
        return;
      }

      const blob = new Blob([response.data], { type: response.headers['content-type'] });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `report_${id}.${format === 'excel' ? 'xlsx' : 'pdf'}`);
      document.body.appendChild(link);
      link.click();
      link.parentNode.removeChild(link);
      window.URL.revokeObjectURL(url);
    } catch (error) {
      alert('Gagal mengunduh file: ' + error.message);
    }
  };

  const handleFileChange = e => {
    setFile(e.target.files[0]);
  };

  const handleUpload = async () => {
    if (!file) return alert("Pilih file Excel terlebih dahulu");
    const formData = new FormData();
    formData.append("file", file);

    try {
      await axios.post("http://127.0.0.1:8000/api/upload-excel", formData);
      alert("Upload berhasil!");
    } catch (err) {
      alert("Upload gagal: " + err.message);
    }
  };

  return (
    <div className="container mt-4">
      <h3 className="mb-4">Pengembangan Fitur Leads Management</h3>

      <div className="mb-4">
        <h5>Upload File Excel</h5>
        <input type="file" className="form-control mb-2" onChange={handleFileChange} />
        <button className="btn btn-primary" onClick={handleUpload}>Upload</button>
      </div>

      <div className="row">
        <div className="col-md-6">
          <h5>Selection Criteria</h5>
          <div className="d-flex flex-wrap gap-2">
            {availableFields.map((field) => (
              <button
                key={field}
                type="button"
                className={`btn btn-sm ${criteria.includes(field) ? 'btn-primary' : 'btn-outline-secondary'}`}
                onClick={() => handleCriteriaChange(field)}
              >
                {field}
              </button>
            ))}
          </div>
        </div>

        <div className="col-md-6">
          <h5>Field Data</h5>
          <div className="d-flex flex-wrap gap-2">
            {availableFields.map(f => (
              <div className="form-check" key={f}>
                <input
                  className="form-check-input"
                  type="checkbox"
                  checked={fields.includes(f)}
                  onChange={() => handleFieldToggle(f)}
                  id={`field-${f}`}
                />
                <label className="form-check-label" htmlFor={`field-${f}`}>
                  {f}
                </label>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="mt-4">
        <input
          type="text"
          className="form-control mb-2"
          placeholder="Nama Report"
          value={reportName}
          onChange={e => setReportName(e.target.value)}
        />
        <button className="btn btn-success" onClick={saveReportTemplate}>Simpan Template</button>
      </div>

      <div className="mt-4">
        <h5>List Report</h5>
        {reports.length === 0 ? (
          <p>Belum ada report disimpan.</p>
        ) : (
          <div className="list-group">
            {reports.map((report, idx) => (
              <div className="list-group-item" key={idx}>
                <h6>{report.name}</h6>
                <p><strong>Kriteria:</strong> {Object.entries(report.criteria).map(([key, val]) => `${key}: ${val}`).join(", ")}</p>
                <p><strong>Field:</strong> {Array.isArray(report.fields) ? report.fields.join(", ") : String(report.fields)}</p>
                <button className="btn btn-danger btn-sm me-2" onClick={() => handleDeleteReport(report.id)}>Hapus</button>
                <button className="btn btn-outline-primary btn-sm me-2" onClick={() => handleExport(report.id, 'pdf')}>Download PDF</button>
                <button className="btn btn-outline-success btn-sm" onClick={() => handleExport(report.id, 'excel')}>Download Excel</button>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default Home;
