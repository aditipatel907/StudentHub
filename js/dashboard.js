fetch("../php/dashboard.php")
    .then(response => response.json())
    .then(student => {

        if (student.error) {
            console.log(student.error);
            return;
        }

        document.getElementById("enrollId").textContent = student.enrollId;
        document.getElementById("fullName").textContent = student.fullName;
        document.getElementById("dept").textContent = student.dept;

        document.getElementById("wdf").textContent = student.attendance.WDF + "%";
        document.getElementById("oops").textContent = student.attendance.OOPS + "%";
        document.getElementById("dsa").textContent = student.attendance.DSA + "%";
        document.getElementById("fcn").textContent = student.attendance.FCN + "%";
        document.getElementById("dm").textContent = student.attendance.DM + "%";

        let overall = (
            student.attendance.WDF +
            student.attendance.OOPS +
            student.attendance.DSA +
            student.attendance.FCN +
            student.attendance.DM
        ) / 5;

        document.getElementById("overallAttendance").textContent = overall.toFixed(1) + "%";
    });