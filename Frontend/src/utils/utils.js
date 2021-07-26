
export const API_URL = "http://localhost:3000/"; //Json placeholder

// export const API_URL = "http://localhost:8000/api/"; //Laravel_api


export const numberWithCommas = (x) => {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
