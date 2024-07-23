const express = require('express');
const bodyParser = require('body-parser');
const { Sequelize, DataTypes, Op } = require('sequelize');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(bodyParser.json());

// Database setup
const sequelize = new Sequelize('doctor_portal', 'root', 'Ali@12021', {
  host: 'localhost',
  dialect: 'mysql',
  port: 3306 // Ensure this is set to the correct MySQL port
});

// Doctor model
const Doctor = sequelize.define('Doctor', {
  doctorname: {
    type: DataTypes.STRING,
    allowNull: false
  },
  speciality: {
    type: DataTypes.STRING,
    allowNull: false
  }
}, {
  tableName: 'doctors',
  timestamps: false // Disable timestamps
});

// Root endpoint
app.get('/', (req, res) => {
  res.send('API is running');
});

// API endpoint to create a doctor
app.post('/doctor', async (req, res) => {
  try {
    const doctor = await Doctor.create({
      doctorname: req.body.doctorname,
      speciality: req.body.speciality,
    });

    res.json({
      message: 'Doctor created successfully.',
      status: 'OK',
      success: 1,
      data: doctor
    });
  } catch (error) {
    console.error("Error creating doctor:", error); // Log the error details
    res.status(500).json({
      message: 'INTERNAL_SERVER_ERROR',
      status: 'INTERNAL_SERVER_ERROR',
      success: 0,
      error: error.message
    });
  }
});

// API endpoint to search for doctors
app.get('/search', async (req, res) => {
  const query = req.query.q; // Get search query from query parameter
  console.log(`Search query: ${query}`); // Log the search query for debugging

  try {
    const results = await Doctor.findAll({
      where: {
        speciality: { [Op.like]: `%${query}%` }
      }
    });
    console.log(`Search results: ${results}`); // Log the search results for debugging
    res.json(results);
  } catch (err) {
    console.error("Error fetching doctors:", err); // Log the error details
    res.status(500).json({ error: 'Internal server error' });
  }
});

// Sync database and start the server
sequelize.sync().then(() => {
  app.listen(3000, () => {
    console.log('Server is running on http://localhost:3000');
  });
}).catch(error => {
  console.error('Unable to connect to the database:', error);
});
