<div class="sellform">
    <h1 class="sftitle">Modify the DataBase</h1>

    <div class="sfcontainer">
        <form
            class="sform"
            action="include/handleModify.inc.php"
            method="post"
            id="sellform">

            <div>
                <div>
                    <label class="sflabel" htmlFor="sfaddress">
                        Current Roll Number
                    </label>
                    <input
                        type="text"
                        class="sfinput"
                        required
                        id="sfaddress"
                        name="roll_no" />
                </div>
            </div>
            <div>
                <p class="sflabel">
                    New details(leave empty to keep unchanged):
                </p>
            </div>
            <div>
                <div>
                    <label class="sflabel" htmlFor="sfaddress">
                        First Name
                    </label>
                    <input
                        type="text"
                        class="sfinput"
                        id="sfaddress"
                        name="firstName" />
                </div>
                <div>
                    <label class="sflabel" htmlFor="sftype">
                        Last Name
                    </label>
                    <input
                        type="text"
                        class="sfinput"
                        id="sftype"
                        name="lastName" />
                </div>
                <div>
                    <label class="sflabel" htmlFor="sftype">
                        Date of Birth
                    </label>
                    <input
                        type="date"
                        class="sfinput"
                        id="sftype"
                        name="dob" />
                </div>
            </div>
            <div>
                <div>
                    <label class="sflabel" htmlFor="sfaddress">
                        Branch
                    </label>
                    <input
                        type="text"
                        class="sfinput"
                        id="sfaddress"
                        name="branch" />
                </div>
                <div>
                    <label class="sflabel" htmlFor="sftype">
                        Phone Number
                    </label>
                    <input
                        type="text"
                        class="sfinput"
                        id="sftype"
                        name="phone_no" />
                </div>
                <div>
                    <label class="sflabel" htmlFor="sftype">
                        Hostel
                    </label>
                    <input
                        type="text"
                        class="sfinput"
                        id="sftype"
                        name="hostel" />
                </div>
            </div>
            <div>
                <div>
                    <label class="sflabel" htmlFor="sfaddress">
                        CPI
                    </label>
                    <input
                        type="number"
                        max="10"
                        min="0"
                        step="0.01"
                        class="sfinput"
                        id="sfaddress"
                        name="CPI" />
                </div>
            </div>

            <input type="submit" value="Update" class="sfsubmit" />
        </form>
    </div>
</div>