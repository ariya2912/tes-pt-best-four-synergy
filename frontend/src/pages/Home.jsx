import React, { useState, useEffect } from "react";
import axios from "axios";
import "bootstrap/dist/css/bootstrap.min.css";
import LeadTable from "../components/LeadTable";
import { getReports, saveReport, deleteReport } from "../services/api.js";

const Home = () => {
  const [availableFields, setAvailableFields] = useState([]);
  const [criteria, setCriteria] = useState({});
  const [fields, setFields] = useState([]);
  const [reportName, setReportName] = useState("");
  const [reports, setReports] = useState([]);
  const [reportData, setReportData] = useState(null);

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

  const handleCriteriaChange = (field, value) => {
    setCriteria((prev) => ({ ...prev, [field]: value }));
  };

  const handleFieldToggle = (field) => {
    setFields((prev) =>
      prev.includes(field) ? prev.filter((f) => f !== field) : [...prev, field]
    );
  };

  const saveReportTemplate = () => {
    if (!reportName || Object.keys(criteria).length === 0 || fields.length === 0) {
      alert("Nama report, kriteria, dan field harus diisi");
      return;
    }
    saveReport({ name: reportName, criteria, fields }).then(() => {
      setReportName("");
      setCriteria({});
      setFields([]);
      fetchReports();
    });
  };

  const deleteReport = name => {
    setReports(prev => prev.filter(r => r.name !== name));
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
          {availableFields.map((field) => (
            <div key={field} className="mb-2">
              <label>{field}</label>
              <input
                type="text"
                className="form-control"
                value={criteria[field] || ""}
                onChange={(e) => handleCriteriaChange(field, e.target.value)}
                placeholder={`Filter by ${field}`}
              />
            </div>
          ))}
        </div>

        <div className="col-md-6">
          <h5>Field Data</h5>
          {availableFields.map(f => (
            <div className="form-check" key={f}>
              <input
                className="form-check-input"
                type="checkbox"
                checked={fields.includes(f)}
                onChange={() => handleCheckbox(f, fields, setFields)}
              />
              <label className="form-check-label">{f}</label>
            </div>
          ))}
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
        <button className="btn btn-success" onClick={saveReport}>Simpan Template</button>
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
                <p><strong>Kriteria:</strong> {report.criteria.join(", ")}</p>
                <p><strong>Field:</strong> {report.fields.join(", ")}</p>
                <button className="btn btn-danger btn-sm me-2" onClick={() => deleteReport(report.name)}>Hapus</button>
                <button className="btn btn-outline-primary btn-sm me-2">Download PDF</button>
                <button className="btn btn-outline-success btn-sm">Download Excel</button>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default Home;
