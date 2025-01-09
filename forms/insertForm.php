<div class="sellform">
    <h1 class="sftitle">Insert into the DataBase</h1>

    <div class="sfcontainer">
        <form
            class="sform"
            action="include/handleInsert.inc.php"
            method="post"
            id="sellform">
            <div>
                <div>
                    <label class="sflabel" htmlFor="sfaddress">
                        First Name
                    </label>
                    <input
                        type="text"
                        class="sfinput"
                        required
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
                        required
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
                        required
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
                        required
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
                        required
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
                        required
                        id="sftype"
                        name="hostel" />
                </div>
            </div>
            <div>
                <div>
                    <label class="sflabel" htmlFor="sfaddress">
                        First Name
                    </label>
                    <input
                        type="number"
                        max="10"
                        min="0"
                        value="0"
                        step="0.01"
                        class="sfinput"
                        required
                        id="sfaddress"
                        name="CPI" />
                </div>
            </div>

            <input type="submit" value="Submit" class="sfsubmit" />
        </form>
    </div>
</div>