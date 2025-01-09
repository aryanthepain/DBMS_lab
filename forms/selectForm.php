<div class="sellform">
    <h1 class="sftitle">Search in the DataBase</h1>

    <div class="sfcontainer">
        <form
            class="sform"
            action="handleSelect.php"
            method="post"
            id="sellform">
            <div>
                <div>
                    <div>
                        <label class="sflabel" htmlFor="sfaddress">
                            Roll Number
                        </label>
                        <input
                            type="number"
                            class="sfinput"
                            id="sfaddress"
                            min="1"
                            step="1"
                            name="roll_no" />
                    </div>
                </div>

                <h3>
                    OR
                </h3>

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
                </div>



            </div>

            <input type="submit" value="Search" class="sfsubmit" />
        </form>
    </div>
</div>